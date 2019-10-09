<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\BaseController;
use App\Models\ProductDescription;
use App\Http\Interfaces\FrontInterface;
use App\Services\Errors;
use App\Services\Order\getOrder;
use App\Services\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends BaseController implements FrontInterface
{

    /**
     * @var getOrder
     */
    private $order;

    /**
     * @var Product
     */
    private $product;


    /**
     * ProductController constructor.
     * @param getOrder $order
     * @param Product $product
     */
    public function __construct(getOrder $order, Product $product)
    {
        parent::__construct();
        $this->order = $order;
        $this->product = $product;
    }

    /**
     * @param int $id
     * @return Response
     * страница продукта
     */
    public function show(int $id) : Response
    {

        try
        {
            $product = ProductDescription::with('product')->findOrFail($id);

            $order_data = $this->order->getArrayData('products');
            $order_products = [];

            if($order_data['products'])
            {
                /*
                 * Товары, которые в корзине
                 * Нужно для конпки "купить" и "в корзине"
                 */
                $order_products = $order_data['products']->pluck('id')->toArray();
            }

            $view_data = [
                'product' => $product,
                'order_products' => $order_products,
            ];

            return response()->view('front.product.show', $view_data);
        }
        catch (ModelNotFoundException $th)
        {
            return redirect('/')->withError(\Lang::get('product.not_found'));
        }
    }

    /**
     * Добавляет товар в избранные
     * @param Request $request
     * @return Response
     */
    public function addToFavorite(Request $request) : Response
    {
        try
        {
            $this->product->addToFavorites($request->get('id'));

            return response()->json([
                'status' => 'success'
            ]);
        }catch(\Throwable $th)
        {
            $error = new Errors($th);
            return $error->ajaxError();
        }
    }

    /**
     * Удаляет из избранного
     * @param Request $request
     * @return Response
     */
    public function removeFavorites(Request $request) : Response
    {
        try
        {
            $this->product->removeFromFavorites($request->get('id'));

            return response()->json([
                'status'    => 'success'
            ]);
        }catch(\Throwable $th)
        {
            $error = new Errors($th);
            return $error->ajaxError();
        }
    }
}
