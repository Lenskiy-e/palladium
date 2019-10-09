<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CategoryDescriptionRequest as StoreRequest;
use App\Http\Requests\CategoryDescriptionRequest as UpdateRequest;
use App\Models\CategoryDescription;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
/**
 * Class CategoryDescriptionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CategoryDescriptionCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\CategoryDescription');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/categorydescription');
        $this->crud->setEntityNameStrings('categorydescription', 'Категории');
        $this->crud->addButtonFromModelFunction('line', 'getChildrens', 'getChildrens', 'end');

        $this->crud->allowAccess('reorder');
        $this->crud->enableReorder('title', 10);
        $this->crud->logFrom = 'categorydescription';
        $this->crud->addButtonFromView('line','showLog','show_log','end');
        /*
        * Для вывода дочерних категорий
        */
        if($this->request->get('category_id') !== NULL){
            $this->crud->addButtonFromModelFunction('line', 'getParent', 'getParent', 'end');
            $this->crud->addClause('where','parent_id',$this->request->get('category_id'));
        }

        // Добавляем фильтры
        $this->addFilters();

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        $this->crud->addField([
            'label' => 'Название',
            'type' => 'text',
            'name' => 'title',
            'tab'   => 'Описание',
        ]);

        $this->crud->addField([
            'label' => 'Префикс',
            'type' => 'text',
            'name' => 'prefix',
            'tab'   => 'Мета'
        ]);

        $this->crud->addField([
            'label' => 'H1',
            'type' => 'text',
            'name' => 'h1',
            'tab'   => 'Мета'
        ]);

        $this->crud->addField([
            'label' => 'Category text top',
            'type' => 'tinymce',
            'name' => 'description_top',
            'tab'   => 'Описание'
        ]);

        $this->crud->addField([
            'label' => 'Category text bottom',
            'type' => 'tinymce',
            'name' => 'description_bottom',
            'tab'   => 'Описание'
        ]);

        $this->crud->addField([
            'label' => 'meta title',
            'type' => 'text',
            'name' => 'meta_title',
            'tab'   => 'Мета'
        ]);

        $this->crud->addField([
            'label' => 'meta description',
            'type' => 'textarea',
            'name' => 'meta_description',
            'tab'   => 'Мета'
        ]);


         $this->crud->addField([
            'label' => 'Фото категории',
            'name' => 'image',
            'type' => 'image',
            'tab'   => 'Данные',
            'upload' => true,
            'crop' => false,
            'aspect_ratio' => 1,
            'entity' => 'Category',
            'model' => 'App\Models\Category'
        ]);

        // $this->crud->addField([
        //     'label' => 'SEO Url Категории',
        //     'type' => 'text',
        //     'name' => 'slug',
        //     'tab'   => 'Данные'
        // ]);


        // Новый ЧПУ
        $this->crud->addfield([
            'label'  => 'Поле object_type таблицы urls',
            'name'   => 'object_type',
            'type'   => 'hidden',
            'entity' => 'url',
            'model'  => 'App\Models\Url',
            'value'  => 'category'
        ]);
        $this->crud->addfield([
            'label'      => 'SEO Url товара',
            'name'       => 'url',
            'type'       => 'text',
            'tab'        => 'Мета',
            'entity'     => 'url',
            'model'      => 'App\Models\Url',
            'attributes' => [
                'readonly' => 'readonly'
            ]
        ]);

        $this->crud->addField(
            [   // select_from_array
                'name' => 'status',
                'label' => "Статус",
                'tab'   => 'Данные',
                'type' => 'select_from_array',
                'options' => [1 => 'Вкл', 0 => 'Выкл'],
                'allows_null' => false,
                'entity' => 'Category',
                'model' => 'App\Models\Category'
            ]
        );

        $this->crud->addField(
            [   // select_from_array
                'name' => 'main',
                'label' => "Главное меню",
                'tab'   => 'Данные',
                'type' => 'select_from_array',
                'options' => [1 => 'Главное меню', 0 => 'Не главное меню'],
                'allows_null' => false,
                'entity' => 'Category',
                'model' => 'App\Models\Category'
            ]
        );

        $this->crud->addField([
            'name' => 'icon',
            'label' => 'Иконка svg/png',
            'type' => 'text',
            'prefix' => '#',
            'tab' => 'Описание',
            'entity' => 'Category',
            'model' => 'App\Models\Category'
        ]);

        $this->crud->addField([
            'name' => 'sort_order',
            'label' => 'Порядок сортировки',
            'type' => 'number',
            'tab' => 'Данные',
            'entity' => 'Category',
            'model' => 'App\Models\Category',
            'default' => 0
        ]);

        $this->crud->addField([
            'name' => 'volume_weight',
            'label' => 'Объемный вес',
            'type' => 'number',
            'tab' => 'Данные',
            'suffix' => 'кг',
            'entity' => 'Category',
            'model' => 'App\Models\Category'
        ]);

        $this->crud->addField(
          [
              'label' => "Спецификации",
              'type' => 'select2_multiple',
              'name' => 'attributes',
              'entity' => 'attributes',
              'attribute' => 'title',
              'model' => "App\Models\Attributes",
              'pivot' => true,
              'tab' => 'Связи'
          ]
        );

        $this->crud->addField(
          [
              'label' => "Фильтры",
              'type' => 'select2_multiple',
              'name' => 'filters',
              'entity' => 'filters',
              'attribute' => 'title',
              'model' => "App\Models\Attributes",
              'pivot' => true,
              'tab' => 'Связи'
          ]
        );

        $this->crud->addField(
            [
                'label' => "Родительская категория",
                'type' => 'select2_nested',
                'name' => 'parent_id',
                'entity' => 'parent',
                'attribute' => 'title',
               // 'model' => "App\Models\CategoryDescription",
                'tab' => 'Связи'
            ]
        );

        $this->crud->addColumns([
            [
                'label' => 'Название',
                'type'  => 'text',
                'name'  => 'title'
            ],

            [
                'label'     => 'Родитель',
                'type'      => 'text',
                'name'      => 'parent_id',
                'entity'    => 'parent',
                'attribute' => 'title',
                'model'     => 'App\Models\CategoryDescription',
            ]

        ]);
        // Вложенность
        $this->crud->addfield([
            'label'  => 'уровень вложенности',
            'name'   => 'depth',
            'type'   => 'hidden'
        ]);
        $this->crud->getCurrentEntry();
        if($this->crud->entry && $this->crud->entry->url->url){
            $this->crud->addfield([
                'label'  => 'Ссылка',
                'name'   => 'href',
                'tab'    => 'Мета',
                'type'   => 'href',
                'value'  =>  url($this->crud->entry->url->url),
            ]);
        }

        // add asterisk for fields that are required in CategoryDescriptionRequest
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
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
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
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    // Backpack фильтры
    private function addFilters(){

        $this->crud->addFilter([
            'name' => 'depth',
            'type' => 'dropdown',
            'label'=> 'Уровень вложенности',
          ],function(){
              $levels = array();
              $categories = CategoryDescription::orderBy('depth','DESC')->first();
              if($categories){
                $category_levels = $categories->depth;
                for($i = 1; $i <= $category_levels; $i++){
                    $levels[$i] = $i;
                }
              }
              return $levels;
          }, function ($value) {
              $this->crud->addClause('where', 'depth', $value);
           }
        );

        $this->crud->addFilter(
            [
            'name'          => 'name',
            'type'          => 'select2_ajax',
            'label'         => 'Поиск',
            'placeholder'   => 'Введите название'
            ],url('category/name'),
            function($value)
            {
                $this->crud->addClause('where', 'depth', 3);
                $this->crud->addClause('where', 'id', $value);
            }
        );
    }

    public function showLog($id){

        $category = Category::find($id);
        $categoryDescription = CategoryDescription::find($id);

        $data['from'] = Input::get('log_from');
        $data['to'] = Input::get('log_to');
        $data['translation'] = $categoryDescription->getTranslatableAttributes();
        $data['rows'][] = $category->getLog($id,$data['from'],$data['to']);
        $data['rows'][] = $categoryDescription->getLog($id,$data['from'],$data['to']);
        return view('data_log',compact('data'));
    }

    // Перегрузка родительского метода
    public function destroy($id){
        try
        {
            $this->crud->hasAccessOrFail('delete');
            $this->crud->setOperation('delete');

            $category = CategoryDescription::find($id);
            // Удаление всех связанных с продуктом данных
            $category->category()->delete();
            $category->attributes()->detach();
            $category->filters()->detach();
            $category->categoryCost()->detach();
            $category->products()->detach();
            $category->delete();

            return response(1,200);
        }catch(\Throwable $th)
        {
            //TODO exception
            return response('Error',500);
        }
    }

    /*public function search()
    {
        $cd_instance = new CategoryDescription();
        $result = $cd_instance->where('title->' . app()->getLocale(), 'like', '%' . $this->request->input('search')['value'] . '%');

        return $result->get();
    }*/
}
