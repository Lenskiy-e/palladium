<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CategoryCostRequest as StoreRequest;
use App\Http\Requests\CategoryCostRequest as UpdateRequest;
use App\Models\CategoryCost;
use Illuminate\Support\Facades\Input;

/**
 * Class CategoryCostCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CategoryCostCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\CategoryCost');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/categorycost');
        $this->crud->setEntityNameStrings('categorycost', 'category_costs');
        $this->crud->logFrom = 'categorycost';
        $this->crud->addButtonFromView('line','showLog','show_log','end');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        $this->crud->addField([
            'label' => 'Название группы категорий',
            'type' => 'text',
            'name' => 'title'
        ]);

        $this->crud->addField([
            'label' => 'Стоимость категорий',
            'type' => 'text',
            'name' => 'cost',
            'suffix' => 'грн'
        ]);

        $this->crud->addField([
            'label' => 'Категории в группе',
            'type' => 'select2_multiple',
            'name' => 'categories',
            'entity' => 'categories',
            'attribute' => 'title',
            'model' => "App\Models\CategoryDescription",
            'pivot' => true,
        ]);

        $this->crud->addColumn([
            'label' => 'Название',
            'type' => 'text',
            'name' => 'title'
        ]);

        $this->crud->addColumn([
            'label' => 'Стоимость',
            'type' => 'text',
            'name' => 'cost',
            'suffix' => ' грн'
        ]);
        // TODO: remove setFromDb() and manually define Fields and Columns
        //$this->crud->setFromDb();

        // add asterisk for fields that are required in CategoryCostRequest
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

    public function showLog($id){

        $category = CategoryCost::find($id);

        $data['from'] = Input::get('log_from');
        $data['to'] = Input::get('log_to');
        $data['translation'] = array();
        $data['rows'][] = $category->getLog($id,$data['from'],$data['to']);
        return view('data_log',compact('data'));
    }
}
