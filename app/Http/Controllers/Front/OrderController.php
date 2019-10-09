<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\BaseController;
use App\Http\Requests\OrdersRequest;
use App\Models\Order;
use App\Services\Errors;
use App\Services\Order\getOrder;
use App\Services\Order\getView;
use App\Services\Order\setOrder;
use App\Services\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends BaseController
{

    /**
     * @var setOrder
     */
    protected $set_order;
    /**
     * @var getOrder
     */
    protected $get_order;
    /**
     * @var Order
     */
    protected $order;
    /**
     * @var getView
     */
    protected $view;
    /**
     * @var User
     */
    protected $user;

    /**
     * OrderController constructor.
     * @param getOrder $get_order
     * @param setOrder $set_order
     * @param getView $view
     * @param Order $order
     * @param User $user
     */
    public function __construct(getOrder $get_order, setOrder $set_order, getView $view, Order $order, User $user)
    {
        parent::__construct();
        $this->set_order    = $set_order;
        $this->get_order    = $get_order;
        $this->order        = $order;
        $this->view         = $view;
        $this->user         = $user;
    }


    /**
     * @return View|Response
     */
    public function index()
    {
        try
        {
            $data = $this->get_order->getArrayData();

            if(!$data['order'])
            {
                if(url()->previous() === url('/order'))
                {
                    return redirect('/')->withError(Lang::get('common.empty_cart'));
                }
                return redirect()->back()->withError(Lang::get('common.empty_cart'));
            }
            return $this->view->getOrder($data);
        }catch (\Throwable $th)
        {
            $error = new Errors($th);
            return $error->staticError();
        }
    }


    /**
     * @param Request $request
     * @return Response
     * @throws \Throwable
     * Создание заказа через ajax запросы
     */
    public function store(Request $request) : Response
    {

        try {
            // если заказ создан, но корзина пуста
            $order = $this->order->getOrder();

            $result = $this->set_order->addProduct($order->id, (int)$request->get('product_id'));

            if(!$result)
            {
                throw new \Error('Error while adding order');
            }
        }
        catch(ModelNotFoundException $th)
        {
            // если заказ не создан
            $order = $this->set_order->createOrder($request);

            if(!$order)
            {
                throw new \Error('Error while adding order');
            }
        }
        catch (\Throwable $th) {
            $error = new Errors($th);
            return $error->ajaxError();
        }

        return response()->json([
            'status'    => 'success'
        ]);
    }


    /**
     * @param Request $request
     * @param $id
     * @return Response
     * оформление заказа
     */
    public function update(OrdersRequest $request, $id) : Response
    {

        try
        {
            $this->set_order->completeOrder($request);
            return redirect('/thank');
        }
        catch (ValidationException $th)
        {
            $error = $th->errors();
            return back()->withErrors($error)->withInput($request->input());
        }
        catch(\Throwable $th)
        {
            $error = new Errors($th);
            return $error->staticError();
        }
    }

    /**
     * @param Request $request
     * @return Response
     * удаление товара с корзины
     */
    public function deleteProduct(Request $request) : Response
    {
        try
        {
            $this->set_order->deleteProduct($request->get('id'));
            return response()->json(['status' => 'success']);
        }catch(\Throwable $th) {
            $error = new Errors($th);
            return $error->ajaxError();
        }
    }

    /**
     * @param Request $request
     * @return Response
     * Отдает нужную вьюху с данными
     */
    public function ajaxInfo(Request $request) : Response
    {

        try
        {
            $type = $request->get('type');

            $data = $this->get_order->getArrayData();

            if(!$data['products'])
            {
                return response()->json('empty');
            }

            if($type === 'modal')
            {
                return $this->view->getOrderModal($data);
            }

            if($type === 'products')
            {
                return $this->view->getOrderProducts($data);
            }

            if($type === 'totals')
            {
                return $this->view->getOrderTotals($data);
            }

            if($type === 'user_exists')
            {
                return $this->view->getUserExist();
            }

        }
        catch(\Throwable $th)
        {
            $error = new Errors($th);
            return $error->ajaxError();
        }

    }

    /**
     * @param Request $request
     * @return Response
     * Проверяет зарегистрирован ли пользователь
     */
    public function checkUser(Request $request) : Response
    {
        try
        {
            $result = $this->user->checkUserInOrder($request->only(['email','phone']));
            return response()->json($result);
        }catch(\Throwable $th)
        {
            $error = new Errors($th);
            return $error->ajaxError();
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * Обновление товаров в корзине
     */
    public function cart(Request $request, $id) : Response
    {
        try
        {
            if(isset($request->cart_count))
            {
                $this->set_order->updateOrderProducts($request, $id);
                $this->set_order->updateOrderInfo($request, $id);
                $this->set_order->updateOrderTotals();
            }else{
                $this->set_order->clearOrder($id);
            }
            return response(['status' => 'success']);
        }catch(\Throwable $th)
        {
            $error = new Errors($th);
            return $error->ajaxError();
        }
    }

    /**
     * Добавляет промокод к заказу
     * @param Request $request
     * @return Response
     */
    public function promo(Request $request) : Response
    {
        $status = 'success';
        $message = '';
        try
        {
            $result = $this->set_order->calculatePromo($request->get('code'));

            if(!empty($result))
            {
                $status = $result['status'];
                $message = $result['message'];
            }
        }
        catch (ModelNotFoundException $th)
        {
            $this->set_order->calculatePromoPrice();

            $status = 'not found';
            $message = Lang::get('order.promo-not-found');
        }
        catch(\Throwable $th)
        {
            $this->set_order->calculatePromoPrice();

            $error = new Errors($th);
            return $error->ajaxError();
        }

        return response()->json([
            'status'    => $status,
            'message'   => $message
        ]);
    }



}
