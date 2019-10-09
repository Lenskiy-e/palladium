<?php

namespace Tests\Feature;

use App\Models\ProductDescription;
use App\Models\Url;
use Tests\TestCase;


class FrontProductTest extends TestCase
{
    /**
     * @var ProductDescription
     */
    private $product_instance;

    /**
     * FrontProductTest constructor.
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->product_instance = new ProductDescription();
    }

    /**
     * Проверка, что все урлы в базе отдают 200
     * Это важно для гугла
     */
    public function testAllProductsAreAnswerOk()
    {
        $urls = Url::where('object_type', 'product')->get();
        foreach ($urls as $url)
        {
            $response = $this->get(url($url->url));

            $response->assertStatus(200);
        }
    }

    /**
     * Проверяем есть ли в активных товарах кнопка "купить"
     */
    public function testAvailableToOrder()
    {
        $products = $this->product_instance::where('edit_status',3)->whereHas('product', function($query){
            $query
                ->where('active',1)
                ->where('available',3);
        })->with('product')->get();

        foreach ($products as $product)
        {
            $response = $this->get(url($product->url->url));

            $response->assertStatus(200);
            $response->assertDontSee(\Lang::get('product.edit_alert'));
            $response->assertSee(\Lang::get('product.text_add_cart'));
        }
    }

    /**
     * Проверяем, что товары на редактировании
     * отдают 200 ответ с предупреждением
     */

    public function testEditingProducts()
    {
        $products = $this->product_instance::where('edit_status','!=',3)->get();

        foreach ($products as $product)
        {
            $response = $this->get(url($product->url->url));

            $response->assertStatus(200);
            $response->assertSee(\Lang::get('product.edit_alert'));
        }
    }

    /**
     * Проверяем, чтобы у не активных товаров
     * не было кнопки "заказать"
     */

    public function testNotAvailableToOrderProducts()
    {
        $products = $this->product_instance::where('edit_status',3)->whereHas('product', function($query){
            $query->where('available','!=',3);
        })->with('product')->get();

        foreach ($products as $product)
        {
            $response = $this->get(url($product->url->url));

            $response->assertStatus(200);
            $response->assertDontSee(\Lang::get('product.text_add_cart'));
        }
    }
}
