<?php

namespace Tests\Feature;

use App\Models\CategoryDescription;
use App\Models\Manufacturer;
use App\Models\Url;
use Tests\TestCase;

class FrontManufacturerTest extends TestCase
{
    /**
     * @var Manufacturer
     */
    private $manufacturer_instance;

    /**
     * FrontManufacturerTest constructor.
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->manufacturer_instance = new Manufacturer();
    }


    /**
     * Проверяем что все урлы дают 200
     * Важно для сео
     */
    public function testAllManufacturersAreAnswerOk()
    {
        $urls = Url::where('object_type', 'manufacturer')->get();
        foreach ($urls as $url)
        {
            $response = $this->get(url($url->url));

            $response->assertStatus(200);
        }
    }

    public function testManufacturerPage()
    {
        $manufacturers = $this->manufacturer_instance->all();

        foreach ($manufacturers as $manufacturer)
        {
            $response = $this->get($manufacturer->url->url);
            $response->assertStatus(200);
            $response->assertSee($manufacturer->title);
        }
    }

    /**
     * Проверяем выводятся ли категории
     * и фильтры категорий на странице
     * производителя
     */
    public function testCategoriesExistOnPage()
    {
        $manufacturers = $this->manufacturer_instance::whereHas('activeProducts')->get();

        foreach ($manufacturers as $manufacturer)
        {

            /**
             * категории в активными продуктами
             * проверяем только первую страницу пагинации
             */
            $categories = $manufacturer->getCategories()->paginate(env('CATEGORIES_PER_PAGE'));

            $response = $this->get($manufacturer->url->url);

            foreach ($categories as $category)
            {
                $response->assertSee($category->title);

                $filter_text = $category->title . ' (' . $category->activeProducts()->count() . ')';

                $response->assertSeeText($filter_text);
            }
        }
    }

    /**
     * Проверяем выводятся ли товары
     */
    public function testProductsOnPage()
    {
        $manufacturers = $this->manufacturer_instance::whereHas('activeProducts')->get();

        foreach ($manufacturers as $manufacturer)
        {
            $response = $this->get($manufacturer->url->url);

            $categories =$manufacturer->getCategories()->get();

            foreach ($categories as $category)
            {
                foreach ($category->activeProducts()->paginate(env('PRODUCTS_PER_PAGE')) as $activeProduct)
                {
                    $response->assertSee($activeProduct->title);
                }
            }
        }
    }

    public function testManufacturersWithoutCategories()
    {
        $manufacturers = $this->manufacturer_instance->doesnthave('activeProducts')->get();

        foreach ($manufacturers as $manufacturer)
        {
            $response = $this->get($manufacturer->url->url);

            $response->assertSee(\Lang::get('manufacturer.empty'));
            $response->assertDontSee(\Lang::get('filter.reset'));
        }
    }
}
