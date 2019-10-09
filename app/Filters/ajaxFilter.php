<?php
namespace App\Filters;

use App;
use App\Models\Parameter;
use Illuminate\Database\Eloquent\Model;
use App\Services\Filter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Errors;

class ajaxFilter
{

    /**
     * @var Filter
     */
    private $filter;

    /**
     * ajaxFilter constructor.
     * @param Filter $filter
     */
    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @param Model $model
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * Generate filter data
     */
    public function generate(Model $model, Request $request) : Response
    {

        $filters = $this->filter->getActives($request->get('param') ?? []);
        $active_filters = array_keys($filters);

        $products_count = $this->filter->getParametersCount($model,$active_filters);

        $url = $this->generateUrl($request);

        $products_data = $this->filter->getFilteredProducts($model, $active_filters);

        $data = [
            'active_filters'        => $active_filters,
            'actives'               => $filters,
            'model'                 => $model,
            'products_count'        => $products_count
        ];

        return response()->json([
            'status'    => 'success',
            'count'     => $products_data->count(),
            'filter'    => $this->filter->getView('parameters', $data ),
            'url'       => $url
        ]);
    }

    /**
     * @param Request $request
     * @return string
     * Генерация URL фильтра
     * для статической фильтрации
     */
    private function generateUrl(Request $request) : string
    {

        $filters = array();

        $locale = App::getLocale();

        $url = '';

        if(!$request->get('param'))
        {
            $result_url = $this->addPriceParamsToUrl($url, $request);
            return $result_url;
        }

        // Получение данных с полученных параметров
        $parameter_obj = new Parameter();

        $params = $parameter_obj->select(['id','title','attribute_id'])->whereIn('id', $request->get('param'))->orderBy('attribute_id')->orderBy('id')->get();

        if(!$params->count())
        {
            $result_url = $this->addPriceParamsToUrl($url, $request);
            return $result_url;
        }

        $params_id = array();

        $url .= "/filter/";

        /*
         * генерация массива параметров
         * ключем выступает приведенное к ЧПУ виду название атрибута
         * параметрами выступают объекты класса Parameter
         */
        foreach ($params as $param) {

            $filters[str_for_url($param->attribute->title)][] = $param;

            $params_id[] = $param->id;
        }

        /*
         * вносим все фильтры в строку урла
         */

        foreach ($filters as $key => $params) {

            $url .= str_for_url($key) . "-";

            foreach ($params as $param) {

                $url .= str_for_url(json_decode($param->title)->$locale) . "-";
            }
        }

        $url = preg_replace("~\-$~", "/params/", $url);

        $url .= implode('-', $params_id);

        $result_url = $this->addPriceParamsToUrl($url, $request);

        return $result_url;
    }

    /**
     * Добавляет ценовой фильтр в урл
     * @param string $url
     * @param Request $request
     * @return string
     */
    protected function addPriceParamsToUrl(string $url, Request $request) : string
    {
        $result_url = $this->filter->getUrl($url);

        $separator = $request->get('get_params') ? '&' : '?';

        if( $this->filter->checkIfPriceFilter() )
        {
            $result_url .= $separator . 'filter_price_from=' . $request->get('filter_price_from') . '&filter_price_to=' . $request->get('filter_price_to');
        }

        return $result_url;
    }
}

