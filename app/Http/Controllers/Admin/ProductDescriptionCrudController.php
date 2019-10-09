<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\ProductDescriptionRequest as StoreRequest;
use App\Http\Requests\ProductDescriptionRequest as UpdateRequest;
use App\Models\Product;
use App\Models\CategoryDescription;
use App\Models\ProductDescription;
use App\Models\BackpackUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
/**
 * Class ProductDescriptionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ProductDescriptionCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\ProductDescription');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/productdescription');
        $this->crud->setEntityNameStrings('productdescription', 'Товар');

        /*
        * Если пользователь контент-менеджер
        * убираем стандартные кнопки и
        * добавляем кнопку аналогичну "update"f
        * Но со своим функционалом
        */
        if(backpack_user()->hasRole('content manager')){
         $this->crud->removeButton('update');
         $this->crud->removeButton('delete');
         $this->crud->addButtonFromView('line','content','content','end');
         $this->crud->addButtonFromView('line','selfAppoint','self_appoint','end');
        }

        $this->crud->allowAccess('show');
        $this->crud->addButton('line', 'show', 'button', 'crud::buttons.show', 'end');
        $this->crud->logFrom = 'productdescription';
        $this->crud->addButtonFromView('line','showLog','show_log','end');
        // Получаем список контент менеджеров

        $this->crud->managers = BackpackUser::getManagers();

        /*
        * Если у юзера есть право назначать менеджера
        * на товар, добавляем select со списком юзеров
        */
        if(backpack_user()->hasPermissionTo('appoint user to product')){
            $this->crud->addButtonFromView('line','appoint','appoint','end');
        }

        /*
        * Получаем текущий объект класса
        * ProductDescription, если мы внутри карточки
        * Затем получаем объект продукта и проверяем
        * тип выводимой цены, которая тянется из 1С
        * в блоке цен (ниже)
        */
        $current_obj = $this->crud->getCurrentEntry();
        if($current_obj){
            $product_obj = Product::find($this->crud->getCurrentEntryId());
        }else{
            $product_obj = null;
        }
        // Фильтры
        $this->addFilters();

         /*
        * Блоки описания
        */
        $this->crud->addField([
            'label' => 'Название товара',
            'type' => 'text',
            'name' => 'title',
            'tab'   => 'Описание'
        ]);


        $this->crud->addField([
            'label' => 'Короткое описание',
            'type'  => 'summernote',
            'name' => 'short_description',
            'tab'   => 'Описание'
        ]);

        $this->crud->addField([
            'label' => 'Описание',
            'type'  => 'summernote',
            'name' => 'description',
            'tab'   => 'Описание'
        ]);

        $this->crud->addField([
            'label' => "Главное фото товара",
            'name' => "image",
            'type' => 'browse_custom',
            'tab'   => 'Описание',
            'entity' => 'product',
            'model' => 'App\Models\Product'
        ]);

        $this->crud->addField([
            'label' => "Фотографии товара",
            'name' => "photos",
            'type' => 'browse_multiple_custom',
            'tab'   => 'Описание',
            'multiple' => true
        ]);

        /*
        *Блоки мета данных
        */

        $this->crud->addField([
            'label' => 'H1 товара',
            'type' => 'text',
            'name' => 'h1',
            'tab'   => 'Мета данные'
        ]);

        $this->crud->addField([
            'label' => 'Meta title товара',
            'type' => 'text',
            'name' => 'meta_title',
            'tab'   => 'Мета данные'
        ]);

        $this->crud->addField([
            'label' => 'Meta description товара',
            'type' => 'textarea',
            'name' => 'meta_description',
            'tab'   => 'Мета данные'
        ]);

        $this->crud->addfield([
            'label'  => 'Поле object_type таблицы urls',
            'name'   => 'object_type',
            'type'   => 'hidden',
            'entity' => 'url',
            'model'  => 'App\Models\Url',
            'value'  => 'product'
        ]);
        $this->crud->addfield([
            'label'  => 'SEO Url товара',
            'name'   => 'url',
            'type'   => 'text',
            'tab'    => 'Мета данные',
            'entity' => 'url',
            'model'  => 'App\Models\Url',
        ]);
        /*
        * Блоки данных товара
        * цена, наличие и т.д
        */

        // массив статусов редактирования
        $edit_options = [0 => 'Не отредактированный',1 => 'Редактируется',2 => 'Отредактированный'];

        /*
        * Если пользователь может публиковать
        * добавляем статус в массив опций
        */
        if(backpack_user()->can('publish')){
            $edit_options = array_merge($edit_options,[3 => 'Опубликован']);
        }
        $this->crud->addfield([
            'label' => 'Статус редактирования',
            'name'  => 'edit_status',
            'tab'   => 'Данные',
            'type' => 'select_from_array',
            'options' => $edit_options,
            'allows_null' => false,
        ]);

        $this->crud->addfield([
            'label' => 'Наличие товара',
            'name'  => 'available',
            'tab'   => 'Данные',
            'type' => 'select_from_array',
            'options' => [3 => 'В наличии',2 => 'Ожидается',1 => 'Нет на складе', 0 => 'Архивный'],
            'allows_null' => false,
            'entity' => 'product',
            'model' => 'App\Models\Product'
        ]);

        $this->crud->addfield([
            'label' => 'Товар уценен',
            'name'  => 'markdown',
            'tab'   => 'Данные',
            'type' => 'select_from_array',
            'options' => [1 => 'Да', 0 => 'Нет'],
            'allows_null' => false,
            'entity' => 'product',
            'model' => 'App\Models\Product'
        ]);

        $this->crud->addField([
            'label' => 'Причина уценки',
            'type' => 'text',
            'name' => 'markdown_reason',
            'tab'   => 'Данные'
        ]);

        $this->crud->addfield([
            'label' => 'Статус',
            'name'  => 'active',
            'tab'   => 'Данные',
            'type' => 'select_from_array',
            'options' => [1 => 'Вкл', 0 => 'Выкл'],
            'allows_null' => false,
            'entity' => 'product',
            'model' => 'App\Models\Product'
        ]);

        $this->crud->addfield([
            'label' => 'Товар до 18 лет',
            'name'  => 'adult',
            'tab'   => 'Данные',
            'type' => 'select_from_array',
            'options' => [1 => 'Да', 0 => 'Нет'],
            'allows_null' => false,
            'entity' => 'product',
            'model' => 'App\Models\Product'
        ]);

        $this->crud->addfield([
            'label' => 'Количество',
            'name'  => 'quantity',
            'tab'   => 'Данные',
            'type' => 'number',
            'entity' => 'product',
            'model' => 'App\Models\Product'
        ]);

        $this->crud->addfield([
            'label' => 'Минимальное количество',
            'name'  => 'minimum_quantity',
            'tab'   => 'Данные',
            'type' => 'number',
            'entity' => 'product',
            'model' => 'App\Models\Product'
        ]);

        $this->crud->addfield([
            'label' => 'Максимальное количество',
            'name'  => 'maximum_quantity',
            'tab'   => 'Данные',
            'type' => 'number',
            'entity' => 'product',
            'model' => 'App\Models\Product'
        ]);

        $this->crud->addfield([
            'label' => 'Объемный вес',
            'name'  => 'volume_weight',
            'tab'   => 'Данные',
            'type' => 'number',
            'entity' => 'product',
            'model' => 'App\Models\Product'
        ]);

        $this->crud->addfield([
            'label' => 'EAN',
            'name'  => 'ean',
            'tab'   => 'Данные',
            'type' => 'number',
            'entity' => 'product',
            'model' => 'App\Models\Product'
        ]);

        $this->crud->addfield([
            'label' => 'SKU',
            'name'  => 'sku',
            'tab'   => 'Данные',
            'type' => 'text',
            'entity' => 'product',
            'model' => 'App\Models\Product'
        ]);

        $this->crud->addfield([
            'label' => 'Модель',
            'name'  => 'model',
            'tab'   => 'Данные',
            'type' => 'text',
            'entity' => 'product',
            'model' => 'App\Models\Product'
        ]);

        $this->crud->addfield([
            'label' => 'Артикул',
            'name'  => 'vendor',
            'tab'   => 'Данные',
            'type' => 'text',
            'entity' => 'product',
            'model' => 'App\Models\Product'
        ]);

         $this->crud->addfield([
            'label' => 'Порядок сортировки',
            'name'  => 'sort_order',
            'tab'   => 'Данные',
            'type' => 'number',
            'entity' => 'product',
            'model' => 'App\Models\Product',
            'default' => 0
        ]);

        /*
        * Блок связей
        */
        $this->crud->addField([   // Select
            'label' => "Главная категория",
            'type' => 'select2_from_ajax_custom',
            'name' => 'category_id',
            'tab'   => 'Связи',
            'entity' => 'mainCategory',
            'attribute' => 'title',
            'model' => "App\Models\CategoryDescription",
            'method' => 'post',
            'data_source' => url("api/category"),
            'placeholder' => "Выберите категорию",
            'minimum_input_length' => 2,
            'pivot' => false,
            'translatable'  => true,
        ]);

        $this->crud->addField([   // Select
            'label' => "Категории",
            'type' => 'select2_from_ajax_multiple_custom',
            'name' => 'category',
            'tab'   => 'Связи',
            'entity' => 'category',
            'attribute' => 'title',
            'model' => "App\Models\CategoryDescription",
            'method' => 'post',
            'data_source' => url("api/category"),
            'placeholder' => "Выберите категорию",
            'minimum_input_length' => 2,
            'pivot' => true,
            'translatable'  => true,
        ]);

        $this->crud->addField([   // Select
            'label' => "Производитель",
            'type' => 'select2_from_ajax',
            'name' => 'manufacturer_id',
            'tab'   => 'Связи',
            'entity' => 'manufacturer',
            'attribute' => 'title',
            'model' => "App\Models\Manufacturer",
            'method' => 'post',
            'data_source' => url("api/manufacturer"),
            'placeholder' => "Выберите производителя",
            'minimum_input_length' => 2,
            'pivot' => false,
        ]);

        $this->crud->addField([   // Select
            'label' => "Прайс-агрегаторы",
            'type' => 'select2_multiple',
            'name' => 'aggregators',
            'tab'   => 'Связи',
            'entity' => 'aggregators',
            'attribute' => 'name',
            'model' => "App\Models\Aggregators",
            'pivot' => true
        ]);

        /*
        * Если мы изменяем товар
        */
        if($product_obj){
            /*
            * Блок цен
            */
            if($product_obj->price_mark == 0){
            $this->crud->addfield([
                'label' => 'РРЦ',
                'name'  => 'rrc_price',
                'tab'   => 'Цены и скидки',
                'type' => 'number',
                'entity' => 'product',
                'model' => 'App\Models\Product'
            ]);
            }elseif($product_obj->price_mark == 1){
                $this->crud->addfield([
                    'label' => 'Базовая цена',
                    'name'  => 'base_price',
                    'tab'   => 'Цены и скидки',
                    'type' => 'number',
                    'entity' => 'product',
                    'model' => 'App\Models\Product'
                ]);
            }

            $this->crud->addfield([
                'label' => 'Акционная цена',
                'name'  => 'sale_price',
                'tab'   => 'Цены и скидки',
                'type' => 'number',
                'entity' => 'product',
                'model' => 'App\Models\Product'
            ]);

            /*
            * Блок Спецификаций
            */

            // Если у товара есть категория
            if($this->crud->getCurrentEntry()->category_id){
              $data = [];
              $attributes = CategoryDescription::find($this->crud->getCurrentEntry()->category_id)->attributes();
              foreach ($attributes->get() as $att) {
                $product_parameters = $att->parameter()->get();
                $product_parameters->push('-');

                $data[] = [
                  'title' => $att->title,
                  'parameters' => $att->parameter()->get(),
                  'multiply' => $att->multiply
                ];
              }

              $this->crud->addField([
                'label' => 'Спецификации',
                'tab' => 'Спецификации',
                'type' => 'parameters',
                'name' => 'parameters',
                'entity' => 'parameters',
                'attribute' => 'title',
                'pivot' => true,
                'select_all' => false,
                'data' => $data,
                'allows_null' => true,
              ]);
                /*
                * Блок Похожих товаров
                */

                // Проверяем есть ли "похожие" продукты

                $similar_attr = $this->crud->entry->similar()->first();

                if($similar_attr){
                    $similar_attr = $similar_attr->pivot->attribute_id;
                }

                $this->crud->addField([
                    'label'       => 'Объеденять по атрибуту',
                    'type'        => 'select_from_array',
                    'tab'         => 'Похожие товары',
                    'name'        => 'similar_attribute',
                    'options'     => $attributes->get()->pluck('title','id'),
                    'allows_null' => true,
                    'default'     => $similar_attr
                ]);

                $this->crud->addField([
                    'label' => "Похожие товары",
                    'type' => "select2_from_ajax_multiple_custom",
                    'tab'    => 'Похожие товары',
                    'translatable' => true, // если attribute переводится
                    'name' => 'similar',
                    'entity' => 'similar',
                    'model' => 'App\Models\ProductDescription',
                    'attribute' => 'title',
                    'method' => 'post',
                    'data_source' => url("api/product"),
                    'placeholder' => "Выберите продукты",
                    'minimum_input_length' => 2,
                    'pivot' => true,
                ]);
            }

            // Аксессуары товары
            $this->crud->addField([
                'label' => "Аксессуары",
                'type' => "select2_from_ajax_multiple_custom",
                'tab'    => 'Рекомендуемые товары и аксессуары',
                'translatable' => true, // если attribute переводится
                'name' => 'accessories',
                'entity' => 'accessories',
                'model' => 'App\Models\ProductDescription',
                'attribute' => 'title',
                'method' => 'post',
                'data_source' => url("api/product"),
                'placeholder' => "Выберите продукты",
                'minimum_input_length' => 2,
                'pivot' => true,
            ]);

            if($this->crud->entry->url->url){
                $this->crud->addfield([
                    'label'  => 'Ссылка',
                    'name'   => 'href',
                    'type'   => 'href',
                    'tab'    => 'Мета данные',
                    'value'  =>  url($this->crud->entry->url->url),
                ]);
            }

        }

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

       // $this->crud->addColumns(['title','slug']);

       $this->crud->addColumn([   // Select
           'label' => "Фото",
           'type' => 'image',
           'name' => 'product.image'
       ]);

        $this->crud->addColumn([
            'label' => 'Название',
            'type' => 'text',
            'name' => 'title'
        ]);

        $this->crud->addColumn([
            'label' => 'Статус редактирования',
            'type' => 'select_from_array',
            'name' => 'edit_status',
            'options' => [0 => 'Не отредактированный',1 => 'Редактируется',2 => 'Отредактированный',3 => 'Опубликован']
        ]);

        $this->crud->addColumn([   // Select
            'label' => "Главная категория",
            'type' => 'text',
            'name' => 'mainCategory.title',
        ]);

        $this->crud->addColumn([   // Select
            'label' => "Производитель",
            'type' => 'text',
            'name' => 'manufacturer.title',
        ]);


        $this->crud->addColumn([   // Select
            'label' => "Цена",
            'type' => 'text',
            'name' => 'product.rrc_price',
        ]);

        // add asterisk for fields that are required in ProductDescriptionRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        if(!$request->url){
            $url = Str::slug($request->title) . ".html";
            $request->merge(['url' => $url]);
        }else{

            if(!preg_match("~([a-zA-Z0-9\-]+)(\.html)$~", $request->url)){
                $new_url = preg_replace("~(.*)(\.html)~", "$1", $request->url);
                $new_url = preg_replace("~([^a-zA-Z0-9\-])~", "", $new_url) . '.html';
                $request->merge(['url' => $new_url]);
            }
        }

        $request = ProductDescription::addMain($request);
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {

        // your additional operations before save here

        /*
        * Недоработка пака
        * если инпут photos ранее был заполнен
        * а потом очищен, то он не меняет свое значение в бд на пустое.
        * Фикс
        */

        if(!$request->photos){
            $request->request->add(['photos' => null]);
        }
        /*
        * Сортировка фото
        */
        else{
            /*
            * Создаем 2 массива:
            * non_sorted_photos для фото, у которых указан порядок 0
            * sorted_photos которые отсортированы
            */
            $non_sorted_photos = [];
            $sorted_photos = [];

            // Убеждаемся, что массив photo_order не пуст
            if($request->photo_order){
                foreach($request->photo_order as $key => $order){
                    /*
                    * Если есть порядок сортировки
                    * Вносим фото в sorted_photos с ключем равным порядку сортировки
                    *
                    */
                    if($order > 0){
                        $sorted_photos[$order] = $request->photos[$key];
                    }
                    // Если сортировка стоит 0, не трогаем
                    else{
                        $non_sorted_photos[] = $request->photos[$key];
                    }
                }
                ksort($sorted_photos);
                $photos = array_merge($non_sorted_photos,$sorted_photos);
                $request->merge(['photos' => $photos]);
            }
        }

        // заполнение полей таблицы manager_product
        ProductDescription::contentStatus($request);

        //Транслитерация для ЧПУ, если поле url не заполнено
        if(!$request->url){
            $url = Str::slug($request->title) . ".html";
            $request->merge(['url' => $url]);
        }else{

            if(!preg_match("~([a-zA-Z0-9\-]+)(\.html)$~", $request->url)){
                $new_url = preg_replace("~(.*)(\.html)~", "$1", $request->url);
                $new_url = preg_replace("~([^a-zA-Z0-9\-])~", "", $new_url) . '.html';
                $request->merge(['url' => $new_url]);
            }
        }

        $request = ProductDescription::addMain($request);

        $redirect_location = parent::updateCrud($request);

        /*
        * Похожие продукты
        * Добавляем после обновления товара из-за
        * бэкпака, который сам аттачит продукты
        * даже если не указан аттрибут
        */
        $this->crud->model->addSilimar($request);

        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }


// Фильтры
    public function addFilters(){
        $this->crud->addFilter([
            'name' => 'manufacturer_id',
            'type' => 'select2',
            'label'=> 'Поизводитель',
        ], function () {
            return \App\Models\Manufacturer::all()->keyBy('id')->pluck('title', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'manufacturer_id', $value);
        });

        $this->crud->addFilter([
          'name' => 'category_id',
          'type' => 'select2',
          'label'=> 'Категория',
        ], function () {
            return \App\Models\CategoryDescription::all()->keyBy('id')->pluck('title', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'category_id', $value);
        });

        $this->crud->addFilter(
            [
            'name' => 'edit_status',
            'type' => 'select2',
            'label' => 'Статус редактирования',
            ], function() {
                return [0 => 'Не отредактированный',1 => 'Редактируется',2 => 'Отредактированный'];
            }, function ($value) {
                $this->crud->addClause('where','edit_status',$value);
            }
        );
    }

    public function appoint(Request $request){

        ProductDescription::appoint($request);
    }

    public function show($id){
        $this->crud->hasAccessOrFail('show');
        $this->crud->setOperation('show');

        $id = $this->crud->getCurrentEntryId() ?? $id;

        foreach ($this->crud->columns as $key => $column) {
            if (array_key_exists('model', $column) && array_key_exists('autoset', $column) && $column['autoset']) {
                $this->crud->removeColumn($column['name']);
            }

            if ($column['type'] == 'row_number') {
                $this->crud->removeColumn($column['name']);
            }

            if (isset($column['visibleInShow']) && $column['visibleInShow'] == false) {
                $this->crud->removeColumn($column['name']);
            }
        }

        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.preview').' '.$this->crud->entity_name;

        // Удаляем все кнопки
        $this->crud->removeAllButtons();

        // remove bulk actions colums
        $this->crud->removeColumns(['blank_first_column', 'bulk_actions','photos','action']);

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getShowView(), $this->data);
    }

    public function showLog($id){

        $product = Product::find($id);
        $productDescription = ProductDescription::find($id);

        $data['from'] = Input::get('log_from');
        $data['to'] = Input::get('log_to');
        $data['translation'] = $productDescription->getTranslatableAttributes();
        $data['rows'][] = $product->getLog($id,$data['from'],$data['to']);
        $data['rows'][] = $productDescription->getLog($id,$data['from'],$data['to']);
        return view('data_log',compact('data'));
    }

    // Перегрузка родительского метода
    public function destroy($id){
        $this->crud->hasAccessOrFail('delete');
        $this->crud->setOperation('delete');

        $product = ProductDescription::find($id);
        // Удаление всех связанных с продуктом данных
        $product->product()->delete();
        $product->aggregators()->detach();
        $product->parameters()->detach();
        $product->manager()->detach();
        $product->category()->detach();
        $product->sales()->detach();
        $product->delete();
    }

}
