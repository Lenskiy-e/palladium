<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CategoryRequest as StoreRequest;
use App\Http\Requests\CategoryRequest as UpdateRequest;

/**
 * Class CategoryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CategoryCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Category');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/category');
        $this->crud->setEntityNameStrings('category', 'categories');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
       // $this->crud->addButtonFromView('top', 'ok', 'show', 'end');
        /*
        **Columns from relation tables
        */


        $this->crud->addField([
            'label' => 'Category Name',
            'type' => 'text',
            'name' => 'title',
            'entity' => 'CategoryDescription',
            'tab'   => 'Описание',
            'model' => 'App\Models\CategoryDescription',
        ]);

        $this->crud->addField([
            'label' => 'Category H1',
            'type' => 'text',
            'name' => 'h1',
            'tab'   => 'Мета',
            'entity' => 'CategoryDescription',
            'model' => 'App\Models\CategoryDescription'
        ]);

        $this->crud->addField([
            'label' => 'Category text top',
            'type' => 'tinymce',
            'name' => 'description_top',
            'tab'   => 'Описание',
            'entity' => 'CategoryDescription',
            'model' => 'App\Models\CategoryDescription'
        ]);

        $this->crud->addField([
            'label' => 'Category text bottom',
            'type' => 'tinymce',
            'name' => 'description_bottom',
            'tab'   => 'Описание',
            'entity' => 'CategoryDescription',
            'model' => 'App\Models\CategoryDescription'
        ]);

        $this->crud->addField([
            'label' => 'Category meta title',
            'type' => 'text',
            'name' => 'meta_title',
            'tab'   => 'Мета',
            'entity' => 'CategoryDescription',
            'model' => 'App\Models\CategoryDescription'
        ]);

        $this->crud->addField([
            'label' => 'Category meta description',
            'type' => 'textarea',
            'name' => 'meta_description',
            'tab'   => 'Мета',
            'entity' => 'CategoryDescription',
            'model' => 'App\Models\CategoryDescription'
        ]);

         $this->crud->addField([
            'label' => 'Фото категории',
            'name' => 'image',
            'type' => 'image',
            'tab'   => 'Данные',
            'upload' => true,
            'crop' => false,
            'aspect_ratio' => 1
        ]);

        $this->crud->addField([
            'label' => 'SEO Url Категории',
            'type' => 'text',
            'name' => 'slug',
            'tab'   => 'Данные',
            'entity' => 'CategoryDescription',
            'model' => 'App\Models\CategoryDescription'
        ]);

        $this->crud->addField(
            [   // select_from_array
                'name' => 'status',
                'label' => "Статус",
                'tab'   => 'Данные',
                'type' => 'select_from_array',
                'options' => [1 => 'Вкл', 0 => 'Выкл'],
                'allows_null' => false,
                // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;
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
                // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;
            ]
        );

        $this->crud->addField([
            'name' => 'icon',
            'label' => 'Иконка svg/png',
            'type' => 'text',
            'prefix' => '#',
            'tab' => 'Описание',
        ]);

        $this->crud->addField([
            'name' => 'order',
            'label' => 'Порядок сортировки',
            'type' => 'number',
            'tab' => 'Данные',
        ]);

        $this->crud->addField([
            'name' => 'volume_weight',
            'label' => 'Объемный вес',
            'type' => 'number',
            'tab' => 'Данные',
            'suffix' => 'кг',
        ]);

       

        // TODO: remove setFromDb() and manually define Fields and Columns
        //$this->crud->setFromDb();

        // add asterisk for fields that are required in CategoryRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
