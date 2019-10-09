<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\AggregatorsRequest as StoreRequest;
use App\Http\Requests\AggregatorsRequest as UpdateRequest;
use App\Models\CategoryDescription;
use App\Models\ProductDescription;
use App\Models\Aggregators;
/**
 * Class AggregatorsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class AggregatorsCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Aggregators');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/aggregators');
        $this->crud->setEntityNameStrings('aggregators', 'Прайс-агрегаторы');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->addField([
            'name' => 'name',
            'label' => 'Название',
            'type' => 'text'
        ]);
        $this->crud->addField([
            'name' => 'link',
            'label' => 'Ссылка',
            'type' => 'text',
            'attributes' => [
                'readonly'=>'readonly'
            ]
        ]);

        $this->crud->addField([
            'name' => 'slug',
            'label' => 'Шаблон',
            'type' => 'hidden',
            'attributes' => [
                'readonly'=>'readonly'
            ]
        ]);

        $this->crud->addField([
            'name' => 'template',
            'label' => 'Шаблон',
            'type' => 'select_from_array',
            'options' => $this->crud->model->all()->pluck('name','slug')->toArray(),
            'allows_null' => true
        ]);

        $this->crud->addfield([
            'label' => 'Статус',
            'name'  => 'status',
            'type' => 'select_from_array',
            'options' => [1 => 'Вкл', 0 => 'Выкл']
        ]);

        //Если прайс-агрегатор уже был создан
        if($this->crud->getCurrentEntry()){
            $categories = CategoryDescription::all()->pluck('title','id')->toArray();

            $this->crud->addField([
                'name' => 'categories',
                'label' => 'Категории',
                'type' => 'select2_from_array',
                'options' => $categories,
                'allows_null' => true,
                'allows_multiple' => true
            ]);
        }

        $this->crud->addColumn([
            'label' => "Название",
            'type' => 'text',
            'name' => 'name'
        ]);

        $this->crud->addColumn([
            'label' => "Ссылка",
            'type' => 'link',
            'name' => 'link'
        ]);
        // add asterisk for fields that are required in AggregatorsRequest
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

    /*
    * Выводит список прайс агрегаторов
    * И список продуктов с пометками
    * Какой продукт есть в прайс агрегаторе
    */
    public function listproducts(){
        $products = ProductDescription::all();
        $aggregators = Aggregators::all();
        return view('aggregator_products', compact('aggregators','products'));
    }
}
