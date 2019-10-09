<?php


namespace App\Services;

use App;
use App\Models\Parameter;
use Exception;
use Illuminate\Http\Request;
use App\Models\ProductDescription;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;


class Filter
{

    /**
     * @var Request
     */
    private $request;

    /**
     * @var string
     * path to view
     */
    private $view_folder;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->view_folder = 'front.filters.filter-';
    }


    /**
     * @param Model $model
     * @param array|null $filters
     * @return array
     * Подсчет кол-ва товаров
     * для каждого параметра-фильтра
     */
    public function getParametersCount(Model $model, Array $filters = null) : array
    {
        $counts = array();

        foreach ($model->filters as $attribute)
        {
            foreach ($attribute->parameter as $param)
            {
                /*
                 * Активные продукты
                 * в которых есть нужные атрибуты
                 */
                $matching_products = $model->activeProducts()
                    ->whereHas('parameters', function($q) use($param)
                    {
                        $q->where('id',$param->id);
                    });

                /*
                 * если активные параметры заданы
                 * отфильтровываем только по наличию
                 * всех параметров
                 */
                if($filters)
                {
                    foreach ($filters as $filter)
                    {
                        $matching_products = $matching_products->whereHas('parameters', function($q) use($filter)
                        {
                            $q->where('id',$filter);
                        });
                    }

                }

                // Если используется ценовой фильтр
                if( $this->checkIfPriceFilter() )
                {

                    $filtered_products = $this->getPriceFilteredProducts($matching_products->get());
                }

                $matching_products = $filtered_products ?? $matching_products;

                $counts[$param->id] = $matching_products->count();
            }
        }
        return $counts;
    }


    /**
     * @param Model $model
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany | \Illuminate\Database\Eloquent\Builder
     */
    public function getFilteredProducts(Model $model, Array $filters = [])
    {
        // По умолчанию - все активные продукты
        $products = $model->activeProducts();


        //Если используется фильтр по параметру
        if ($filters) {
            foreach ($filters as $filter) {
                $products = $products->whereHas('parameters', function($q) use($filter)
                {
                    $q->where('id', $filter);
                });
            }
        }

        //Если юзаем ценовой фильтр
        if( $this->checkIfPriceFilter() )
        {
            $products = $this->getPriceFilteredProducts($products->get());
        }

        return $products;
    }

    /**
     * @param Collection $model
     * @return array
     * @throws Exception
     * Получение цен товаров
     */
    public function getPrices(Collection $model) : array
    {
        $prices = [];

        foreach($model as $price)
        {
            if(method_exists($price,'getPrice'))
            {
                $prices[$price->id] = $price->getPrice()['price'];
            }else
            {
                throw new Exception("Can't find getPrice method", 1);
            }
        }

        $max = $prices ? max($prices) : 0;
        $min = $prices ? min($prices) : 0;

        return [
            'min'   => $min,
            'max'   => $max,
            'from'  => $this->request->get('filter_price_from') ?? $min,
            'to'    => $this->request->get('filter_price_to') ?? $max
        ];
    }

    /**
     * @return bool
     * проверяет, используется ли фильтр по ценам
     */
    public function checkIfPriceFilter() : bool
    {
        if($this->request->isMethod('post'))
        {
            $from_percent = $this->request->get('from_percent') ?? 0;
            $to_percent = $this->request->get('to_percent') ?? 99;

            if($from_percent > 0 || $to_percent < 99)
            {
                return true;
            }
        }

        return $this->request->get('filter_price_from') && $this->request->get('filter_price_to');
    }


    /**
     * @param Collection $products
     * @return \Illuminate\Database\Eloquent\Builder | \App\Models\ProductDescription
     * Получение продуктов по диапазону цен в фильтре
     */
    public function getPriceFilteredProducts(Collection $products)
    {
        $result = $products->map( function($product)
        {

            $price = $product->getPrice()['price'];

            if ($price >= $this->request->get('filter_price_from') && $price <= $this->request->get('filter_price_to'))
            {
                return $product;
            }
        });

        return ProductDescription::whereIn('id', $result->pluck('id'));
    }

    /**
     * @param String $middle
     * @return string
     * Генерация сео урла для фильтра
     */
    public function getUrl(string $middle) : string
    {

        $url = get_current_url($this->request->get('base_url'));

        $url .= $middle . '.html';

        if($this->request->get('get_params'))
        {
            $url .= '?' . $this->request->get('get_params');
        }

        return $url;
    }


    /**
     * @param string $view
     * @param array $data
     * @return string
     * получение вьюхи
     */
    public function getView(string $view, array $data) : string
    {
        return View::make($this->view_folder . $view, $data)->render();
    }

    public function getActives(array $ids)
    {
        return Parameter::whereIn('id', $ids)
            ->get()->pluck('name','id')->toArray();
    }

}
