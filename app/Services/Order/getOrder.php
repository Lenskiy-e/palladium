<?php

namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class getOrder
{

    /**
     * @var Order
     */
    protected $order;

    /**
     * getOrder constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order =  $order;
    }

    /**
     * Получение данных о товарах
     * в заказе
     * @param Order $order
     * @return Response
     * @throws \Throwable
     */
    public function getOrderProductsData(Order $order) : Response
    {
        try
        {
            $count = 0;

            foreach ($order->product()->get() as $product)
            {

                $count += $product->pivot->count;
            }

            $result = [
                'count'     => $count,
                'amount'    => $order->getAttribute('total_amount')
            ];

            return response($result);
        }catch(\Throwable $th)
        {
            throw $th;
        }

    }


    /**
     * Получение данных о заказе
     * в формате json
     * @param string|null $key
     * @return JsonResponse
     * @throws \Throwable
     */
    public function getJsonData(string $key = null) : JsonResponse
    {

        try
        {
            $result = $this->getOrderData();

            if($key && array_key_exists($key , $result))
            {
                return Response::json( [$key => $result[$key]] );
            }

            return Response::json($result);
        }catch(\Throwable $th)
        {
            throw $th;
        }

    }

    /**
     * Получение данных о заказе
     * в массиве
     * @param string|null $key
     * @return array
     * @throws \Throwable
     */
    public function getArrayData(string $key = null) : array
    {
        try
        {
            $result = $this->getOrderData();

            if($key && array_key_exists($key , $result))
            {
                return [$key => $result[$key]];
            }

            return $result;
        }catch(\Throwable $th)
        {
            throw $th;
        }
    }

    /**
     * формирование данных
     * о заказе для методов
     * getArrayData
     * getJsonData
     * @return array
     */
    protected function getOrderData() : array
    {

        $result = [
            'order' => [],
            'products' => [],
            'user' => []
        ];

        try
        {
            $order = $this->order->getOrder();
        }catch(ModelNotFoundException $th)
        {
            return $result;
        }

        $products = $order->product()->get();
        $user = $order->user()->first();

        $result = [
            'order' => $order,
            'products' => $products,
            'user' => $user
        ];

        return $result;
    }

}
