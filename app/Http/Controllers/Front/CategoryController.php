<?php

namespace App\Http\Controllers\Front;

use App\FilterBuilder;
use App\Http\Controllers\BaseController;
use App\Services\Errors;
use App\Services\Order\getOrder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\CategoryDescription;
use Illuminate\Support\Facades\View;
use App\Http\Interfaces\PaginateFrontInterface;
use App\Http\Interfaces\FilteredFrontInterface;


class CategoryController extends BaseController implements PaginateFrontInterface, FilteredFrontInterface
{

    /**
     * @var filter_data
     */
    protected $filter_data;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var FilterBuilder
     */
    protected $filter;
    /**
     * @var getOrder
     */
    protected $get_order;

    /**
     * CategoryController constructor.
     * @param Request $request
     * @param FilterBuilder $filter
     * @param getOrder $get_order
     */
    public function __construct(Request $request, FilterBuilder $filter, getOrder $get_order)
    {
        parent::__construct();
        $this->request = $request;
        $this->filter = $filter;
        $this->get_order = $get_order;
    }


    /**
     * @param int $id
     * @param int|null $page
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     * @throws \Throwable
     */
    public function show(int $id, int $page = null)
    {
        // Кол-во постов на странице
        $per_page = env('PRODUCTS_PER_PAGE');

        $category = CategoryDescription::with(['filters','children','products'])->findOrFail($id);

        // Если уже не используется статический фильтр
        if(!$this->filter_data)
        {
            $this->filter_data = $this->filter->build('static', $category, $this->request);
        }

        $products = $this->filter_data->get('products');

        // Проверяем текущую страницу
        $check = check_current_page($category, $products->count(), $per_page, $this->request, $page, $this->filter_data->get('url'));
        if($check){
            return $check;
        }

        /**
         * Если у категории нет подкатегорий,
         * создаем объект пагинации продуктов
         * и запихиваем во вьюху
         * Если подкатегории есть - выводим их
         * в другой вьюхе
         */

        if(!$category->children()->count()){

            $pagination_path = $this->filter_data->get('url');

            $products = $products
                ->paginate($per_page,['*'],'page',$page ?? 1)
                ->withPath($pagination_path);

            try
            {
                $order_data = $this->get_order->getArrayData('products');

                $order_products = [];

                if($order_data['products'])
                {
                    $order_products = $order_data['products']->pluck('id')->toArray();
                }
            }catch(ModelNotFoundException $th)
            {
                $order_products = [];
            }


            $data = [
                'filter'            => $this->filter_data->get('view'),
                'price_filter'      => $this->filter_data->get('filter_price_view'),
                'get_params'        => get_params_to_str($this->request, ['page','filter_price_from','filter_price_to']),
                'order_products'    => $order_products,
                'category'          => $category,
                'products'          => $products
            ];


            return response()->view( 'front.category.products', $data);
        }

        return response()->view('front.category.categories', ['category' => $category]);
    }


    /**
     * @param $data
     * @param int|null $page
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     * @throws \Throwable
     * генерация данных при статическом фильтре
     */
    public function filter($data, int $page = null)
    {

        $model = CategoryDescription::with('products')->where('id',$data->get("instance_id"))->first();

        $this->request->merge($data->toArray());

        $this->filter_data = $this->filter->build('static', $model, $this->request);

        return $this->show($data->get("instance_id"), $page);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function ajaxFilter(Request $request)
    {
        try
        {
            $category = CategoryDescription::where('id',$request->id)->with('filters')->first();

            return $this->filter->build('ajax', $category, $request);
        }catch(\Throwable $th)
        {
            $error = new Errors($th);
            return $error->ajaxError();
        }

    }
}

