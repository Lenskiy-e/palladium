<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\ContentStatisticRequest as StoreRequest;
use App\Http\Requests\ContentStatisticRequest as UpdateRequest;
use App\Models\BackpackUser;
/**
 * Class ContentStatisticCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ContentStatisticCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/content');
        $this->crud->setEntityNameStrings('contentstatistic', 'Статистика заполненых продуктов');
        $this->crud->setModel('App\Models\BackpackUser');
        if(backpack_user()->hasRole('content manager')){
            $this->getManagerProducts(backpack_user()->id);
            $this->crud->setListView('content_statistic');
        }else{
            $this->crud->addClause('whereIn','id', $this->crud->model->managers());
            $this->crud->allowAccess('show');
            $this->crud->addButton('line', 'show', 'button', 'crud::buttons.show', 'end');
            $this->crud->removeButton('update');
            $this->crud->removeButton('delete');
            $this->crud->addColumn([
            'label' => 'Имя сотрудника',
            'type' => 'text',
            'name' => 'name',
            ]); 
        }
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns

        // add asterisk for fields that are required in ContentStatisticRequest
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

    public function show($id){
        $this->getManagerProducts($id);
        $this->crud->setShowView('content_statistic');
        return parent::show($id);
    }

    private function getManagerProducts($id){
        /*
        * Получаем статистические данные о нужном пользователе
        */
        $data = backpack_user()->getManagerProducts($id);
        $this->crud->plan_products = $data['plan_products'];
        $this->crud->overplan_products = $data['overplan_products'];
        $this->crud->plan = $data['plan'];
        $this->crud->total_count = $data['total_count'];
    }
}
