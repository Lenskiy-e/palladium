<?php


namespace App\Services\Order;

use Symfony\Component\HttpFoundation\Response;

class getView
{

    /**
     * @param array $data
     * @return Response
     */
    public function getOrderModal(array $data): Response
    {
        return response()->view('front.order.modal.modal', $data);

    }

    /**
     * @param array $data
     * @return Response
     */
    public function getOrder(array  $data): Response
    {
        return response()->view('front.order.index', $data);
    }

    /**
     * @param array $data
     * @return Response
     */
    public function getOrderProducts(array $data): Response
    {
        return response()->view('front.order.products', ['products' => $data['products']]);
    }

    /**
     * @param array $data
     * @return Response
     */
    public function getOrderTotals(array $data): Response
    {
        return response()->view('front.order.totals', ['order' => $data['order']]);
    }

    /**
     * @return Response
     */
    public function getUserExist() : Response
    {
        return response()->view('front.order.user_exists');
    }
}
