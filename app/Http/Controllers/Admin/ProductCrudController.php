<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\ProductRequest as StoreRequest;
use App\Http\Requests\ProductRequest as UpdateRequest;

/**
 * Class ProductCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ProductCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Product');

        $this->crud->setRoute(config('backpack.base.route_prefix') . '/product');
        $this->crud->setEntityNameStrings('product', 'products');
        /*
        *Переменная для определения какую из цен выводить
        *тянется с 1С
        */
        $product_obj = $this->crud->getCurrentEntry();
        //Фильтры
        $this->addFilters();

        /*
        *Блоки описания
        */
        $this->crud->addField([
          [
              'label' => 'Название товара',
              'type' => 'text',
              'name' => 'title',
              'entity' => 'productDescription',
              'tab'   => 'Описание',
              'model' => 'App\Models\ProductDescription',
          ],
          [
              'label' => 'Короткое описание',
              'type' => 'tinymce',
              'name' => 'short_description',
              'tab'   => 'Описание',
              'entity' => 'productDescription',
              'model' => 'App\Models\ProductDescription'
          ],
          [
              'label' => 'Описание',
              'type' => 'tinymce',
              'name' => 'description',
              'tab'   => 'Описание',
              'entity' => 'productDescription',
              'model' => 'App\Models\ProductDescription'
          ]
        ]);
        
        $this->crud->addField([
            'label' => "Фото товара",
            'name' => "image",
            'type' => 'image',
            'tab'   => 'Описание',
            'upload' => true,
        ], 'both');

        /*
        *Блоки мета данных
        */

        $this->crud->addField([
          [
            'label' => 'H1 товара',
            'type' => 'text',
            'name' => 'h1',
            'tab'   => 'Мета данные',
            'entity' => 'productDescription',
            'model' => 'App\Models\ProductDescription'
          ],
          [
            'label' => 'Meta title товара',
            'type' => 'text',
            'name' => 'meta_title',
            'tab'   => 'Мета данные',
            'entity' => 'productDescription',
            'model' => 'App\Models\ProductDescription'
          ],
          [
            'label' => 'Meta description товара',
            'type' => 'textarea',
            'name' => 'meta_description',
            'tab'   => 'Мета данные',
            'entity' => 'productDescription',
            'model' => 'App\Models\ProductDescription'
          ],
          [
            'label' => 'SEO Url товара',
            'type' => 'text',
            'name' => 'slug',
            'tab'   => 'Мета данные',
            'hint' => 'Будет сформировано автоматически исходя из имени, если оставить пустым',
            'entity' => 'productDescription',
            'model' => 'App\Models\ProductDescription'
          ]
        ]);

        /*
        *Блоки данных товара
        *цена, наличие и т.д
        */

        $this->crud->addfield([
          [
            'label' => 'Товар уценен',
            'name'  => 'markdown',
            'tab'   => 'Данные',
            'type' => 'select_from_array',
            'options' => [1 => 'Да', 0 => 'Нет'],
            'allows_null' => false,
          ],

          [
              'label' => 'Причина уценки',
              'type' => 'text',
              'name' => 'markdown_reason',
              'tab'   => 'Данные',
              'entity' => 'productDescription',
              'model' => 'App\Models\ProductDescription'
          ],

          [
              'label' => 'Статус',
              'name'  => 'active',
              'tab'   => 'Данные',
              'type' => 'select_from_array',
              'options' => [1 => 'Вкл', 0 => 'Выкл'],
              'allows_null' => false,
          ],

          [
              'label' => 'Товар до 18 лет',
              'name'  => 'adult',
              'tab'   => 'Данные',
              'type' => 'select_from_array',
              'options' => [1 => 'Да', 0 => 'Нет'],
              'allows_null' => false,
          ],

          [
              'label' => 'Количество',
              'name'  => 'quantity',
              'tab'   => 'Данные',
              'type' => 'number',
          ],

          [
              'label' => 'Минимальное количество',
              'name'  => 'minimum_quantity',
              'tab'   => 'Данные',
              'type' => 'number',
          ],

          [
              'label' => 'Максимальное количество',
              'name'  => 'maximum_quantity',
              'tab'   => 'Данные',
              'type' => 'number',
          ],

          [
              'label' => 'Объемный вес',
              'name'  => 'volume_weight',
              'tab'   => 'Данные',
              'type' => 'number',
          ],

          [
              'label' => 'EAN',
              'name'  => 'ean',
              'tab'   => 'Данные',
              'type' => 'number',
          ],

          [
              'label' => 'SKU',
              'name'  => 'sku',
              'tab'   => 'Данные',
              'type' => 'text',
          ],

          [
              'label' => 'Модель',
              'name'  => 'model',
              'tab'   => 'Данные',
              'type' => 'text',
          ],
          [
              'label' => 'Артикул',
              'name'  => 'vendor',
              'tab'   => 'Данные',
              'type' => 'text',
          ],
          [
              'label' => 'Порядок сортировки',
              'name'  => 'sort_order',
              'tab'   => 'Данные',
              'type' => 'number'
          ]
        ]);

        /*
        *Блок связей
        */
        $this->crud->addField([
          [   // Select
            'label' => "Главная категория",
            'type' => 'select2',
            'name' => 'category_id',
            'tab'   => 'Связи',
            'entity' => 'mainCategory',
            'attribute' => 'title',
            'model' => "App\Models\CategoryDescription"
          ],
          [   // Select
              'label' => "Производитель",
              'type' => 'select',
              'name' => 'manufacturer_id',
              'tab'   => 'Связи',
              'entity' => 'manufacturer',
              'attribute' => 'title',
              'model' => "App\Models\Manufacturer"
          ]
        ]);
        /*
        *Блок цен
        */
        if($product_obj){
            if($product_obj->price_mark == 0){
            $this->crud->addfield([
                'label' => 'РРЦ',
                'name'  => 'rrc_price',
                'tab'   => 'Цены и скидки',
                'type' => 'number',
            ]);
            }elseif($product_obj->price_mark == 1){
                $this->crud->addfield([
                    'label' => 'Базовая цена',
                    'name'  => 'base_price',
                    'tab'   => 'Цены',
                    'type' => 'number',
                ]);
            }
        }

        

        /*
        **Columns in admin preview
        */

        $this->crud->addColumn([
            'name' => 'ProductDescription.title',
            'label' => 'Name',
            'type' => 'text'
        ]);
        $this->crud->addColumn([   // Select
            'label' => "Главная категория",
            'name' => 'CategoryDescription.title',
            'entity' => 'mainCategory',
            'attribute' => 'title',
            'model' => "App\Models\CategoryDescription"
        ]);
        $this->crud->addColumns(['name', 'description', 'price']);
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        //$this->crud->setFromDb();
        // add asterisk for fields that are required in ProductRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        //$this->crud->getCurrentEntry()->translatable = '';
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        //$this->crud->getCurrentEntry()->translatable = '';
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    //Фильтры
    public function addFilters(){
        $this->crud->addFilter([ // select2 filter
          'name' => 'category_id',
          'type' => 'select2',
          'label'=> 'Категория',
        ], function () {
            return \App\Models\CategoryDescription::all()->keyBy('category_id')->pluck('title', 'category_id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'category_id', $value);
        });


        $this->crud->addFilter([ // select2 filter
          'name' => 'status',
          'type' => 'dropdown',
          'label'=> 'Статус',
        ], [1 => 'Включен', 0 => 'Выключен'], function ($value) { // if the filter is active
            $this->crud->addClause('where', 'active', $value);
        });
    }
}
