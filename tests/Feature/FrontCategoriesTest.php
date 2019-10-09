<?php

namespace Tests\Feature;

use App\Models\CategoryDescription;
use App\Models\Url;
use App\Services\Filter;
use Illuminate\Http\Request;
use Tests\TestCase;


class FrontCategoriesTest extends TestCase
{

    /**
     * @var Filter
     */
    private $filter;

    /**
     * FrontCategoriesTest constructor.
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->filter = new Filter(new Request());
    }

    public function testUrls()
    {
        $categories_urls = Url::where('object_type', 'category')->get();

        foreach ($categories_urls as $url)
        {
            $response = $this->get($url->url);
            $response->assertStatus(200);
        }
    }

    public function testNotFoundCategory()
    {
        $response = $this->get('c-notfoundcategory.html');
        $response->assertStatus(404);
    }

    /**
     * Правильно ли подбирается вьюха
     */
    public function testProductsViewOnCategoryPage()
    {
        $categories = CategoryDescription::whereHas('products')->get();

        foreach ($categories as $category)
        {
            $response = $this->get($category->url->url);
            $response->assertViewIs('front.category.products');
        }
    }


    /**
     * Проверяем правильные ли продукты выводятся
     * на первой странице пагинации категории
     */
    public function testActiveProductsOnCategoryPage()
    {
        $categories = CategoryDescription::whereHas('activeProducts')->with('activeProducts')->get();

        foreach ($categories as $category)
        {
            $response = $this->get($category->url->url);

            foreach ($category->activeProducts()->paginate(env('PRODUCTS_PER_PAGE')) as $product)
            {
                $response->assertSee(
                    $product->title
                );
            }
        }
    }

    /**
     * Правильно ли подбирается вьюха
     */
    public function testCategoriesViewOnCategoryPage()
    {
        $categories = CategoryDescription::whereHas('children')->get();
        foreach ($categories as $category)
        {
            $response = $this->get($category->url->url);
            $response->assertViewIs('front.category.categories');
        }
    }

    /**
     * Проверяем все ли фильтры выводятся
     */
    public function testCategoriesParametersFilters()
    {
        $categories = $this->activeFiltersCategories();

        foreach ($categories as $category)
        {
            $response = $this->get($category->url->url);

            foreach ($category->filters as $filter)
            {
                $response->assertSee($filter->title);
            }
        }
    }

    /**
     * Проверка кол-ва товаров в параметрах фильтров
     */
    public function testCategoriesParametersFiltersProductsCount()
    {
        $categories = $this->activeFiltersCategories();

        foreach ($categories as $category)
        {
            $response = $this->get($category->url->url);

            foreach ($category->filters as $filter)
            {
                foreach ($filter->parameter as $parameter)
                {
                    // Кол-во товаров соответствующие фильтру
                    $counts = $this->filter->getParametersCount($category, [$parameter->id]);

                    // Название параметра
                    $title = json_decode($parameter->title, true);

                    // Поисковой текст
                    $text = $title[app()->getLocale()] . ' (' . $counts[$parameter->id] . ')';

                    $response->assertSeeText($text);
                }
            }
        }
    }

    /**
     * Проверка результатов ajax фильтра
     */
    public function testCategoryAjaxFilters()
    {
        $categories = $this->activeFiltersCategories();

        foreach ($categories as $category)
        {
            foreach ($category->filters as $filter)
            {
                foreach ($filter->parameter as $parameter)
                {
                    // Кол-во товаров соответствующие фильтру
                    $counts = $this->filter->getParametersCount($category, [$parameter->id])[$parameter->id];

                    $response = $this->json('post', url('filter/generate'), [
                        'id'    => $category->id,
                        'base_url'  => $category->url->url,
                        'param'   => [$parameter->id],
                    ]);

                    $response
                        ->assertStatus(200)
                        ->assertJsonStructure(['count', 'filter', 'status', 'url'])
                        ->assertJson(['count' => $counts, 'status' => 'success']);
                }
            }
        }
    }

    /**
     * Проверка результатов статического фильтра
     */
    public function testCategoryStaticFilters()
    {
        $categories = $this->activeFiltersCategories();

        foreach ($categories as $category)
        {
            foreach ($category->filters as $filter)
            {
                foreach ($filter->parameter as $parameter)
                {
                    $response = $this->json('post', url('filter/generate'), [
                        'id'    => $category->id,
                        'base_url'  => $category->url->url,
                        'param'   => [$parameter->id],
                    ]);

                    // Запрос отфильтрованных результатов
                    $filter_response = $this->get(url($response->decodeResponseJson()['url']));

                    // Отфильтрованные продукты
                    $products = $this->filter->getFilteredProducts($category, [$parameter->id])->paginate(env('PRODUCTS_PER_PAGE'));

                    foreach ($products as $product)
                    {
                        $filter_response->assertSee($product->title);
                    }
                }
            }
        }
    }

    private function activeFiltersCategories()
    {
        return CategoryDescription::whereHas('activeProducts')->whereHas('filters')->with(['filters', 'activeProducts'])->get();
    }
}
