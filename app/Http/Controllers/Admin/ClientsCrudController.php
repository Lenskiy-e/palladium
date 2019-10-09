<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\ClientsRequest as StoreRequest;
use App\Http\Requests\ClientsRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class ClientsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ClientsCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\User');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/clients');
        $this->crud->setEntityNameStrings('clients', 'clients');
        $this->crud->addClause('where', 'is_admin', 0);
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->addFields([

            [
                'label'     => 'Имя',
                'type'      => 'text',
                'name'      => 'name',
                'tab'       => 'Общая информация'
            ],
            [
                'label'     => 'Фамилия',
                'type'      => 'text',
                'name'      => 'last_name',
                'entity'    => 'profile',
                'tab'       => 'Общая информация'
            ],
            [
                'label'     => 'Отчество',
                'type'      => 'text',
                'name'      => 'patronymic',
                'entity'    => 'profile',
                'tab'       => 'Общая информация'
            ],
            [
                'label'     => 'Почта',
                'type'      => 'text',
                'name'      => 'email',
                'tab'       => 'Общая информация'
            ],
            [
                'label'     => 'Телефон',
                'type'      => 'number',
                'name'      => 'phone',
                'tab'       => 'Общая информация'
            ],
            [
                'label'     => 'Дата рождения',
                'type'      => 'date',
                'name'      => 'birthday',
                'entity'    => 'profile',
                'tab'       => 'Маркетинг'
            ],
            [
                'label'     => 'Пол',
                'type'      => 'select_from_array',
                'options'   => [0 => 'М', 1 => 'Ж'],
                'name'      => 'gender',
                'entity'    => 'profile',
                'tab'       => 'Маркетинг'
            ],
            [
                'label'     => 'Автомобиль',
                'type'      => 'checkbox',
                'name'      => 'auto',
                'entity'    => 'marketing',
                'tab'       => 'Маркетинг'
            ],
            [
                'label'     => 'Рассылка',
                'type'      => 'checkbox',
                'name'      => 'mailing',
                'entity'    => 'marketing',
                'tab'       => 'Маркетинг'
            ],
            [
                'label'     => 'Дети',
                'type'      => 'checkbox',
                'name'      => 'children',
                'entity'    => 'marketing',
                'tab'       => 'Маркетинг'
            ],
            [
                'label'     => 'Увлечения',
                'type'      => 'select2_multiple',
                'name'      => 'hobbies',
                'entity'    => 'hobbies',
                'attribute' => 'title',
                'pivot'     => 'true',
                'tab'       => 'Маркетинг'
            ]

        ]);

        $this->crud->addColumns([

            [
                'label' => 'Имя',
                'type'  => 'text',
                'name'  => 'name',
            ],
            [
                'label'     => 'Фамилия',
                'type'      => 'text',
                'name'      => 'last_name',
                'entity'    => 'profile'
            ],
            [
                'label'     => 'Отчество',
                'type'      => 'text',
                'name'      => 'patronymic',
                'entity'    => 'profile'
            ],
            [
                'label'     => 'Почта',
                'type'      => 'text',
                'name'      => 'email',
            ],
            [
                'label' => 'Телефон',
                'type'  => 'text',
                'name'  => 'phone',
            ]
        ]);

        // add asterisk for fields that are required in ClientsRequest
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
