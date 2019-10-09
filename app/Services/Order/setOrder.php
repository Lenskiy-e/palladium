<?php

namespace App\Services\Order;

use App\Events\UserCreated;
use App\Models\Order;
use App\Models\ProductDescription;
use App\Models\Promo;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;

class setOrder
{

    /**
     * @var Order
     */
    protected $order;

    /**
     * setOrder constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Добавляет товар к заказу
     * @param int $order_id
     * @param int $product_id
     * @return bool
     * @throws \Throwable
     */
    public function addProduct(int $order_id, int $product_id) : bool
    {

        try
        {
            $order = $this->order->getOrder($order_id);

            $product = ProductDescription::find($product_id, ['id']);

            $product_prices = $product->getPrice();

            $price = $product_prices['price'];

            $order->product()->attach($product->getAttribute('id'),
                [
                    'cost'          => $price,
                    'count'         => 1
                ]
            );

            $order->increment('total_amount', $price);
            $order->increment('total_count', 1);

            return true;
        }
        catch (ModelNotFoundException $th)
        {
            // TODO exception
            return false;
        }catch(\Throwable $th)
        {
            throw $th;
        }

    }

    /**
     * @param Request $request
     * @return Order
     * @throws \Throwable
     */
    public function createOrder(Request $request) : Order
    {
        try
        {
            $user_id = null;

            if(Auth::check())
            {
                Order::where('user_id', Auth::user()->id)->where('complete', false)->delete();
                $user_id = Auth::user()->id;
            }

            $order = Order::create([
                'user_id'       => $user_id,
                'total_amount'  => 0
            ]);

            Redis::set('order_id', $order->id);

            $this->addProduct($order->id, $request->get('product_id'));

        }catch(\Throwable $th)
        {
            throw $th;
        }

        return $order;
    }

    /**
     * @param int $id
     * @return bool
     * @throws \Throwable
     */
    public function deleteProduct(int $id) : bool
    {

        $order = $this->order->getOrder();

        if($order)
        {
            try
            {
                $order->product()->detach([$id]);
                $this->calculatePromo();
                $this->updateOrderTotals($order->id);

                return true;

            }catch(\Throwable $th)
            {
                // TODO exceptions
                throw $th;
            }
        }
    }

    /**
     * Обновление цены и кол-ва товара в заказе
     * @param int|null $id
     * @return bool
     */
    public function updateOrderTotals(int $id = null) : bool
    {
        try
        {
            $order = $this->order->getOrder($id);

            $products = $order->product()->get();
            $count = 0;
            $cost = 0;

            foreach ($products as $product)
            {
                $count += $product->pivot->count;
                $cost += $product->pivot->cost * $product->pivot->count;
            }

            if($count === 0)
            {
                return $this->deleteOrder($order->id);
            }

            $order->update([
                'total_amount'  => $cost,
                'total_count'   => $count
            ]);

            return true;

        }catch(\Throwable $th)
        {
            // TODO exception
            return false;
        }
    }

    /**
     * Обновление товара в заказе
     * @param Request $request
     * @param int|null $id
     * @return bool
     * @throws \Throwable
     */
    public function updateOrderProducts(Request $request, int $id = null) : bool
    {
        $order = $this->order->getOrder($id);

        $products = $request->get('cart_count') ?? null;
        if($products)
        {

            $counts = [];

            foreach ($products as $p_id => $count)
            {
                $counts[$p_id] = ['count' => $count];
            }

            $order->product()->sync($counts);

            return true;
        }
    }

    public function updateOrderInfo(Request $request, Int $id)
    {
        //
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws \Throwable
     */
    public function clearOrder(int $id = null) : bool
    {
        $order = $this->order->getOrder($id);
        $delete = $order->product()->detach();
        $this->updateOrderTotals($id);
        return $delete;
    }


    /**
     * @param int|null $id
     * @throws \Exception
     */
    public function deleteOrder(int $id = null) : void
    {
        $order = $this->order->getOrder($id);

        if($order)
        {
            $order->product()->detach();
            $order->delete();
        }
    }

    /**
     * Подтверждение заказа
     * @param Request $request
     */
    public function completeOrder(Request $request) : void
    {
        $order = $this->order->getOrder();

        if($order) {
            $user = Auth::user();

            /*
             * Если юзер не был залогинен
             * до оформления заказа
             * регаем его и добавляем к заказу
             */
            if (!$user) {
                $user = $this->getUserFromOrder($request);

                $order->update(['user_id' => $user->id]);
            }

            $promo = $order->promocode()->first();

            if(!$promo)
            {
                $this->calculatePromoPrice();
            }

            if($promo)
            {
                if($promo->checkUser($order)) // если код уже юзался
                {
                    $this->calculatePromoPrice();
                    throw ValidationException::withMessages(['promocode' => Lang::get('order.promo-already-used')]);
                }

                $promo->user()->attach($user->id);

                if($promo->unique)
                {
                    $promo->update([
                        'used'  => 0
                    ]);
                }

            }

            $order->update([
                'complete' => 1,
                'phone' => $request->get('phone'),
                'getter_first_name' => $request->get('name'),
                'getter_last_name' => $request->get('last_name')
            ]);

            Redis::del('order_id');
        }
    }

    /**
     * Просчитываем стоимость товара
     * @param ProductDescription $product
     * @param Promo|null $promo
     * @return float|int|mixed
     */
    protected function getProductPrice(ProductDescription $product, ?Promo $promo) : float
    {
        $prices = $product->getPrice();
        $price = $prices['price'];

        if($prices['old_price'])
        {
            return $price;
        }

        if($promo && $promo->checkProductPromo($product))
        {
            if($promo->type === 1)
            {
                $price *= (100 - $promo->discount) / 100;
            }

            if($promo->type === 2)
            {
                $price -= $promo->discount;
            }
        }

        return $price;
    }

    /**
     * Получаем юзера для заказа
     * Или создаем его из полей
     * @param Request $request
     * @return User
     */
    protected function getUserFromOrder(Request $request) : User
    {
        $user_instance = new \App\Services\User();

        $user = $user_instance->getUserByKey('phone', $request->only('phone'));

        if(!$user && $request->get('email'))
        {
            $user = $user_instance->getUserByKey('email', $request->only('email'));
        }

        if(!$user)
        {
            $user = User::create($request->only(['name', 'email', 'phone']));

            event(new UserCreated($user));

            $user->profile->update(['last_name' => $request->get('last_name')]);
        }

        $phone = $request->get('phone');

        if ($user->phone !== $phone)
        {
            $this->orderPhones($user,$request->get('phone'));
        }

        return $user;
    }

    /**
     * Если юзер уже зареган и указал другой телефон
     * то мы его добавляем в "доп. телефоны"
     * @param $user
     * @param $phone
     */
    protected function orderPhones($user, $phone) : void
    {
        $additional_phones = json_decode($user->profile->additional_phones);

        $new_phones = [$phone];

        if($additional_phones)
        {
            if(!in_array($phone, $additional_phones) )
            {
                $new_phones = array_merge($additional_phones, [$phone]);
            }else{
                $new_phones = $additional_phones;
            }
        }

        $user->profile->update([
            'additional_phones' => json_encode($new_phones)
        ]);
    }

    /**
     * Просчет цены товаров по промокоду
     * Если $promo === null
     * Пересчитываем продукты по
     * цене товара
     * @param Promo|null $promo
     */
    public function calculatePromoPrice(Promo $promo = null) : void
    {
        $order = $this->order->getOrder();
        $products = $order->product()->get();

        foreach ($products as $product) {
            $price = $this->getProductPrice($product, $promo);

            $order->product()->updateExistingPivot($product->id, ['cost' => $price]);
        }

        if($promo && $promo->unique)
        {
            $promo->update(['used' => 0]);
        }
    }

    /**
     * Поиск и применение промокода
     * К заказу и обновление корзины
     * @param string|null $code
     * @return array
     */
    public function calculatePromo(string $code = null) : array
    {
        $order = $this->order->getOrder();
        $promo = null;

        if($code)
        {
            $promo = Promo
                ::where('active',1)
                ->where('used', 1)
                ->whereDay('expired_at', '>=', Carbon::today())
                ->where('code', $code)
                ->select('id','code','reusable', 'type' ,'discount', 'minimum_amount')
                ->firstOrFail();
        }

        if(!$code)
        {
            $promo = $order->promocode()->first();
        }

        if(!$promo || !$promo->count())
        {
            $this->calculatePromoPrice();
            return [];
        }

        if($order->products_sum < $promo->minimum_amount)
        {
            $this->calculatePromoPrice();
            return [
                'message'   => Lang::get('order.promo-minimum-amount'),
                'status'    => 'not found'
            ];
        }

        if($promo && $promo->checkUser($order))
        {
            $this->calculatePromoPrice();
            return [
                'message'   => Lang::get('order.promo-already-used'),
                'status'    => 'not found'
            ];
        }

        $this->calculatePromoPrice($promo);

        $order->update([
            'promo'  => $promo->id
        ]);

        $this->updateOrderTotals();

        return [];

    }
}
