<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\SalesRequest as StoreRequest;
use App\Http\Requests\SalesRequest as UpdateRequest;

use App\Imports\ProductSalesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Sales;
use App\Models\Set;

/**
 * Class SalesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class SalesCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Sales');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/sales');
        $this->crud->setEntityNameStrings('sales', 'Акции');
        $this->crud->removeButton('create');
        $this->crud->addButtonFromView('top','add_sale','add_sale','end');
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        //$this->crud->setFromDb();


        /*
        ** 
        * Определяем тип скидки
        * Если акция только создается
        * Тип будет выбран из гет параметра
        * Если акция создана - из БД
        * То берем значение поля type
        **
        */

        $sale_type = null;
        $current_model = null;
        if($this->crud->getCurrentEntry()){
            $model = new Sales();
            $current_model = $model->where('id',$this->crud->getCurrentEntryId())->first();
            $sale_type = $current_model->type;
        }elseif ($this->request->get('type') ==! null) {
            $sale_type = $this->request->get('type');
        }

        /*
        **
        * Блок с переменными полей
        * Все переменные со значением variable_field
        * Должны быть массивами, в которых массив данных полей
        **
        */

        // Таблица продуктов с id, названием и новой ценой
        $table = [
            'name' => 'product',
            'label' => 'Товары',
            'type' => 'products_table',
            'entity_singular' => 'product',
            'columns' => [
               'product_id' => 'ID продукта',
               'product_name' => 'Имя продукта',
               'new_price' => 'Акционная цена'
            ],
            'data_source' => url('api/product'),
        ];

        $sale_obj = NULL;
        if($this->crud->getCurrentEntry()){
            $sale_obj = $this->crud->model->find($this->crud->getCurrentEntryId());
            $products = $sale_obj->price_product;
            $value = array();
            foreach($products as $product){
                $value[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->title,
                    'new_price' => $product->pivot->new_price
                ];
            }
            $table = array_merge($table, ['value' => $value]);
        }

        // Поля с категориями и производителями
        $category_manufacturers_fields = [
            [
                'label' => 'Категории',
                'type'  => 'select2_multiple',
                'name'  => 'category',
                'entity' => 'category',
                'attribute' => 'title',
                'pivot' => true
            ],
            [
                'label' => 'Производители',
                'type'  => 'select2_multiple',
                'name'  => 'manufacturer',
                'entity' => 'manufacturer',
                'attribute' => 'title',
                'pivot' => true
            ]
        ];

        // Поля с типом скидки и скидкой
        $discount_fields = [
            [ // select_from_array
                'name' => 'discountType',
                'label' => 'Тип скидки',
                'type' => 'select_from_array',
                'options' => ['0' => '-', '1' => 'грн.', '2' => '%'],
                'allows_null' => false,
                'default' => 'currency',
            ],
            [
                'label' => 'Скидка',
                'name' => 'discount',
                'type' => 'number'
            ]
        ];

        // Поля с таблицей и загрузкой файла
        $table_fields = [
            [
                'label' => 'Файл с продуктами',
                'type' => 'upload',
                'name' => 'products',
                'upload' => true
            ],
            $table
        ];

        // Поля с продуктами, к которым идет подарок
        $product_gifts_field = 
        [
            [
                'label' => "Продукты в акции",
                'type' => "select2_from_ajax_multiple_custom",
                'translatable' => true, // если attribute переводится
                'name' => 'product',
                'entity' => 'product',
                'model' => 'App\Models\ProductDescription',
                'attribute' => 'title',
                'method' => 'post',
                'data_source' => url("api/product"),
                'placeholder' => "Выберите продукты",
                'minimum_input_length' => 2,
                'pivot' => true,
            ],
            [
                'label' => "Подарок",
                'type' => "select2_from_ajax_multiple_custom",
                'translatable' => true, // если attribute переводится
                'name' => 'gift',
                'entity' => 'gift',
                'model' => 'App\Models\ProductDescription',
                'attribute' => 'title',
                'method' => 'post',
                'data_source' => url("api/product"),
                'placeholder' => "Выберите продукты",
                'minimum_input_length' => 2,
                'pivot' => true,
            ]
        ];

        /*
        * Поля с контентом
        */

        $content_fields = [
            [
                'label' => 'Описание',
                'type'  => 'summernote',
                'name' => 'description',
            ],
            [
                'label' => 'Банер',
                'name' => 'baner',
                'type' => 'image'
            ]
        ];

        /*
        * Поля с комплектами
        */
        $sets = null;
        if($current_model){
            $sets = $current_model->sets()->get();
        }

        $sets_field = [
            [
                'label' => 'Комплекты',
                'type'  => 'sets',
                'name'  => 'sets',
                'sets'  => $sets
            ]
        ];

        $product_gifts = [];
        if($current_model){
            $product_gifts = $current_model->gifts;
        }
        // Поля индивидуального подарка к товару
        $gift_field = [
            [
                'label' => 'Подарки',
                'type'  => 'gifts',
                'name'  => 'gift_product',
                'gifts' => $product_gifts
            ]
        ];

        // Поля, которые будут вне зависимости от типа акции/скидки
        $stable_fields = [
            [
                'label' => 'Название',
                'name' => 'name',
                'type' => 'text'
            ],
            [
                'label' => 'Старт акции',
                'type' => 'date_picker',
                'name' => 'start',
                'date_picker_options' => [
                    'language' => 'ru'
                ]
            ],
            [
                'label' => 'Конец акции',
                'type' => 'date_picker',
                'name' => 'end',
                'date_picker_options' => [
                    'language' => 'ru'
                ]
            ],
            [
                'name'  => 'type',
                'type'  => 'hidden',
                'value' => $sale_type
            ]
        ];
        $this->crud->addFields($stable_fields);

        // Сборка полей, в зависимости от get параметра
        $type_fields = [
            'empty' => [$discount_fields, $table_fields],        
            'gift'  => [$content_fields, $category_manufacturers_fields,$product_gifts_field, $gift_field],
            'discounts' => [$content_fields, $discount_fields, $category_manufacturers_fields, $table_fields,],
            'shipping' => [$content_fields, $category_manufacturers_fields, $table_fields],
            'complect' => [$content_fields, $sets_field],
            'credit'   => [$content_fields,$table_fields]
        ];

        if($sale_type){
            foreach ($type_fields[$sale_type] as $fields) {
                foreach ($fields as $field) {
                    $this->crud->addField($field);
                }

            }
        }


        /*$this->crud->addFields([
            
            ,
            [
                'label' => 'Тип акции',
                'name' => 'discountType',
                'type' => 'select_from_array',
                'options' => [0 => 'Скидка грн.', 1 => 'Скидка %', 2 => 'Комплект', 3 => 'Бесплатная доставка', 4 => 'Подарок', 5 => 'Кредит'],
                'allows_null' => false,
                'default' => 0
            ],
            
            [
                'label' => 'Файл с продуктами',
                'type' => 'upload',
                'name' => 'products',
                'upload' => true
            ],
            
        ]);*/

        $this->crud->addColumns([
            [
                'label' => 'Название',
                'type' => 'text',
                'name' => 'name'
            ],
            [
                'label' => 'Банер',
                'name' => 'baner',
                'type' => 'image'
            ]
        ]);

        // add asterisk for fields that are required in SalesRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        if(in_array($this->request->input('type'),['empty','discounts', 'shipping','credit'])){
            if($request->hasFile('products')){
                Excel::import(new ProductSalesImport($this->crud->getCurrentEntryId()), $request->file('products'));
            }else{
                $this->crud->model->attachRequestProducts($this->crud->getCurrentEntryId(),json_decode($request->input('product')));
            }
        }elseif($this->request->input('type') == 'complect'){
            $this->crud->model->addSets($this->crud->getCurrentEntryId(),$request);
        }elseif($this->request->input('type') == 'gift'){
            if($request->input('gift_ar')){
                $this->crud->model->addGifts($this->crud->getCurrentEntryId(),$request);
            }
        }
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $this->crud->model->addSets($this->crud->getCurrentEntryId(),$request);

        if(in_array($this->request->input('type'),['empty','discounts', 'shipping','credit'])){
            if($request->hasFile('products')){
                Excel::import(new ProductSalesImport($this->crud->getCurrentEntryId()), $request->file('products'));
            }else{
                $this->crud->model->attachRequestProducts($this->crud->getCurrentEntryId(),json_decode($request->input('product')));
            }
        }elseif($this->request->input('type') == 'complect'){
            $this->crud->model->addSets($this->crud->getCurrentEntryId(),$request);
        }elseif($this->request->input('type') == 'gift'){
            if($request->input('gift_ar')){
                $this->crud->model->addGifts($this->crud->getCurrentEntryId(),$request);
            }
        }
        
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
