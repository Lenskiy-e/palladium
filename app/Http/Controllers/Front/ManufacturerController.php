<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\BaseController;
use App\Models\Manufacturer;
use App\Services\Errors;
use App\Services\Order\getOrder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Filters\categoriesFilter;
use App\Http\Interfaces\FrontInterface;
use App\Http\Interfaces\PaginateFrontInterface;
use App\Http\Interfaces\FilteredFrontInterface;
use Symfony\Component\HttpFoundation\Response;

class ManufacturerController extends BaseController implements FrontInterface, PaginateFrontInterface, FilteredFrontInterface
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var filter_data
     */
    protected $filter_data;
    /**
     * @var categoriesFilter
     */
    protected $categoriesFilter;

    /**
     * @var Manufacturer
     */
    protected $manufacturer_instance;

    /**
     * @var getOrder
     */
    protected $get_order;


    /**
     * ManufacturerController constructor.
     * @param categoriesFilter $categoriesFilter
     * @param Request $request
     * @param Manufacturer $manufacturer_instance
     * @param getOrder $get_order
     */
    public function __construct(categoriesFilter $categoriesFilter, Request $request, Manufacturer $manufacturer_instance, getOrder $get_order)
    {
        parent::__construct();
        $this->categoriesFilter = $categoriesFilter;
        $this->request = $request;
        $this->manufacturer_instance = $manufacturer_instance;
        $this->get_order = $get_order;
    }

    /**
     * @param int $id
     * @param int|null $page
     * @return \Illuminate\Contracts\View\Factory | \Illuminate\View\View
     */
    public function show(int $id, int $page = null)
    {
        // Кол-во записей на странице
        $per_page = env('CATEGORIES_PER_PAGE');

        $manufacturer = $this->manufacturer_instance->find($id);

        $filter_data = $this->filter_data ?? $this->categoriesFilter->generate($manufacturer, $this->request);

        // Проверяем текущую страницу пагинации

        $categories_data = $filter_data->get('categories');

        $check = check_current_page($manufacturer, $categories_data->count(), $per_page, $this->request, $page);

        if($check){
            return $check;
        }

        $categories = $categories_data->paginate($per_page,['*'],'page',$page ?? 1)
                                        ->withPath(get_current_url($manufacturer->url->url));

        $filter_categories = $filter_data->get('view');

        $get_params = get_params_to_str($this->request, ['page','filter_price_from','filter_price_to', 'param']);

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
          'manufacturer'        => $manufacturer,
          'categories'          => $categories,
          'filter_categories'   => $filter_categories,
          'get_params'          => $get_params,
          'order_products'      => $order_products,
        ];
        return view('front.manufacturer.show', $data);
    }

    /**
     * @param $data
     * @param Int|null $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function filter($data, Int $page = null)
    {
        $manufacturer = $this->manufacturer_instance->find($data['instance_id']);

        $this->request->merge([
            'param' => explode('-',$data['parameter_ids'])
        ]);

        $this->filter_data = $this->categoriesFilter->generate($manufacturer, $this->request);

        return $this->show($data['instance_id'], $page);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxFilter(Request $request) : Response
    {
        try
        {
            $manufacturer = $this->manufacturer_instance->find($request->id);

            $filter_data = $this->categoriesFilter->generate($manufacturer, $request);

            return response()->json([
                'status'    => 'success',
                'count'     => $filter_data->get('categories')->count(),
                'filter'    => $filter_data->get('view'),
                'url'       => $this->categoriesFilter->generateUrl($request)
            ]);
        }catch(\Throwable $th)
        {
            $error = new Errors($th);
            return $error->ajaxError();
        }
    }

}
