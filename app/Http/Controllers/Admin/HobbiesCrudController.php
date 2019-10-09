<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\HobbiesRequest as StoreRequest;
use App\Http\Requests\HobbiesRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class HobbiesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class HobbiesCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Hobbies');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/hobbies');
        $this->crud->setEntityNameStrings('hobbies', 'hobbies');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->addFields([

            [
                'label'     => 'Название',
                'type'      => 'text',
                'name'      => 'title'
            ],
            [
                'label'     => 'Категории',
                'name'      => 'category',
                'type'      => 'select2_multiple',
                'entity'    => 'category',
                'attribute' => 'title',
                'model'     => "App\Models\CategoryDescription",
                'pivot'     => true
            ],
            [
                'label'     => 'Производители',
                'name'      => 'manufacturer',
                'type'      => 'select2_multiple',
                'entity'    => 'manufacturer',
                'attribute' => 'title',
                'model'     => "App\Models\Manufacturer",
                'pivot'     => true
            ]
        ]);

        // TODO: remove setFromDb() and manually define Fields and Columns
        $this->crud->setFromDb();

        // add asterisk for fields that are required in HobbiesRequest
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
