<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\SettingRequest as StoreRequest;
use App\Http\Requests\SettingRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class SettingCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class SettingCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Setting');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/setting');
        $this->crud->setEntityNameStrings('setting', 'settings');
        $this->crud->removeButton('create');
        $this->crud->denyAccess('list');
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        //$this->crud->setFromDb();

        $this->crud->addFields([
            [
                'type'  => 'text',
                'name'  => 'name',
                'label' => 'Имя магазина'
            ],

            [
                'type'  => 'text',
                'name'  => 'meta_title',
                'label' => 'Meta title'
            ],

            [
                'type'  => 'textarea',
                'name'  => 'meta_description',
                'label' => 'Meta description'
            ],

            [
                'type'  => 'email',
                'name'  => 'email',
                'label' => 'Почта владельца сайта'
            ],

            [
                'type'              => 'table',
                'name'              => 'phones',
                'label'             => 'Телефоны магазина',
                'entity_singular'   => 'phone',
                'columns'           => ['phone' => 'Номера']
            ],

            [
                'type'      => 'browse_custom',
                'name'      => 'logo',
                'label'     => 'Логотип магазина',
//                'upload'    => true,
//                'crop'      => true,
            ],

            [
                'type'      => 'browse_custom',
                'name'      => 'placeholder',
                'label'     => 'Изображение по умолчанию',
//                'upload'    => true,
//                'crop'      => true,
            ],
            [
                'type'      => 'browse_custom',
                'name'      => 'favicon',
                'label'     => 'Favicon',
//                'upload'    => true,
//                'crop'      => true,
            ],

            [
                'type'              => 'table',
                'name'              => 'addresses',
                'label'             => 'Адресса магазина',
                'entity_singular'   => 'address',
                'columns'           => [
                    'country'   => 'Страна',
                    'city'      => 'Город',
                    'street'    => 'Адресс',
                    'src'       => 'Ссылка на карту'
                ]
            ],

            [
                'type'              => 'table',
                'name'              => 'work_times',
                'label'             => 'График работы магазина',
                'entity_singular'   => 'time',
                'columns'           => [
                    'weekdays'   => 'Пн - Пт',
                    'sat'      => 'Сб',
                    'sun'    => 'Вс',
                ]
            ],

            [
                'type'  => 'checkbox',
                'name'  => 'tech_works',
                'label' => 'Сайт на тех обслуживании'
            ],

            [
                'type'      => 'select_from_array',
                'name'      => 'footer_template',
                'label'     => 'Шаблон футера',
                'options'   => ['footer' => 'footer']
            ],

        ]);

        // add asterisk for fields that are required in SettingRequest
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
