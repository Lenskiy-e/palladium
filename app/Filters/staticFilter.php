<?php
namespace App\Filters;

use App\Services\Filter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class staticFilter
{
    /**
     * @var App\Services\Filter
     */
    private $filter;

    /**
     * staticFilter constructor.
     * @param Filter $filter
     */
    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }


    /**
     * @param Model $model
     * @param Request $request
     * @return Collection
     * @throws \Exception
     * генерация фильтра
     */
    public function generate(Model $model, Request $request) : Collection
    {

        $active_filters = [];
        $active = [];

        // выделение активных фильтров
        if ($request->get('parameter_ids')) {

            $active = $this->filter->getActives(explode("-", $request->get('parameter_ids')));
            $active_filters = array_keys($active);
        }

        // продукты, который попадают под параметры фильтра

        $products = $this->filter->getFilteredProducts($model, $active_filters);

        $url = get_current_url($model->url->url);

        // если используется СЕО фильтр
        if($request->get('seo_filter'))
        {
            $url .= '/filter/' . $request->get('seo_filter') . '/params/' . $request->get('parameter_ids');
        }

        $products_count = $this->filter->getParametersCount($model,$active_filters);

        $filter_view = $this->filter->getView(
            'parameters',
                    [
                        'active_filters'        => $active_filters,
                        'actives'               => $active,
                        'model'                 => $model,
                        'products_count'        => $products_count
                    ]
        );

        $products_prices = $this->filter->getPrices($products->get());

        $filter_price_view =
                                $products->count() > 1 ?
                                $this->filter->getView('price', $products_prices )
                                    : '';

        return collect([
            'products'          => $products,
            'products_count'    => $products_count,
            'products_prices'   => $products_prices,
            'url'               => $url,
            'view'              => $filter_view,
            'filter_price_view' => $filter_price_view

        ]);

    }
}

