<?php
namespace App\Filters;

use App;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\CategoryDescription;
use Illuminate\Database\Eloquent\Model;
use App\Services\Filter;
use App\Models\Manufacturer;

class categoriesFilter
{

    /**
     * @var Filter
     */
    private $filter;

    /**
     * categoriesFilter constructor.
     * @param Filter $filter
     */
    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @param Model $manufacturer
     * @param Request $request
     * @return Collection
     * Генерация фильтра
     */
    public function generate(Model $manufacturer, Request $request) : Collection
    {

        $active_categories = [];

        if($request->get('param'))
        {
            $active_categories = $request->get('param');
        }

        $categories = $this->getManufacturerCategories($manufacturer->id, $active_categories);

        $view = $this->filter->getView('category',
            [
                'active_filters' => $active_categories,
                'categories'        => $categories->get(),
            ]
        );

        $data = collect([
            'categories'        => $categories,
            'view'              => $view,
        ]);

        return $data;
    }

    /**
     * @param Request $request
     * @return string
     * генерация урла
     */
    public function generateUrl(Request $request) : string
    {
        $url = '';

        $filters = $request->get('param');

        if(!$filters)
        {
            return $url;
        }

        $category_instance = new CategoryDescription();

        $categories = $category_instance->select('title','id')->whereIn('id', $filters)->get();

        $filter_part = '/filter/'; // для красоты урла
        $params_part = '/params/'; // для фильтрации

        foreach ($categories as $category) {

            $filter_part .= str_for_url($category->title) . '-';
            $params_part .= $category->id . '-';
        }
        $url .= trim($filter_part, '-') . trim($params_part, '-');

        return $this->filter->getUrl($url);

    }


    /**
     * @param int $id
     * @param array $categories
     * @return Builder
     * Получение категорий вендора
     */
    protected function getManufacturerCategories(int $id, array $categories) : Builder
    {
        if(!$categories)
        {
            $manufacturer = Manufacturer::find($id);

            return $manufacturer->getCategories();
        }
        return CategoryDescription::whereIn('id',$categories);
    }

    /**
     * @param array $products
     * @return array
     * Получение кол-ва товаров в категоии с текущим вендором
     */
    protected function getCategoryProductsCount(array $products) : array
    {

        $result = [];

        foreach ($products as $category => $product)
        {
            $result[$category] = $product->count();
        }

        return $result;
    }

    /**
     * @param Collection $categories
     * @param int $id
     * @return array
     * получение продуктов производителя и категорий
     */
    protected function getProducts(Collection $categories, int $id) : array
    {
        $result = [];

        foreach ($categories as $category)
        {
            $result[$category->id] = $category->activeProducts()->get();
        }

        return $result;
    }
}

