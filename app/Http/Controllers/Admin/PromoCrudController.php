<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\PromoRequest as StoreRequest;
use App\Http\Requests\PromoRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class PromoCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class PromoCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Promo');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/promo');
        $this->crud->setEntityNameStrings('promo', 'promos');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->addFields([
            [
                'label' => 'Код',
                'name'  => 'code',
                'type'  => 'promocode'
            ],
            [
                'label' => 'Описание',
                'type'  => 'textarea',
                'name'  => 'description'
            ],
            [
                'label'     => 'Многократное использование (любой пользователь, любое кол-во раз)',
                'name'      => 'reusable',
                'type'      => 'select_from_array',
                'options'   => [0 => 'Нет', 1 => 'Да']
            ],
            [
                'label'     => 'Уникальный (используется только 1 раз)',
                'name'      => 'unique',
                'type'      => 'select_from_array',
                'options'   => [0 => 'Нет', 1 => 'Да']
            ],
            [
                'label'     => 'Тип скидки',
                'name'      => 'type',
                'type'      => 'select_from_array',
                'options'   => [1 => '%', 2 => 'грн']
            ],
            [
                'label'     => 'Размер скидки',
                'name'      => 'discount',
                'type'      => 'number',
                'suffix'    => '.00'
            ],
            [
                'label'     => 'Мин. сума заказа',
                'name'      => 'minimum_amount',
                'type'      => 'number',
                'suffix'    => '.00'
            ],
            [
                'label'                 => 'Действует с',
                'name'                  => 'start_at',
                'type'                  => 'date_picker',
                'date_picker_options'   => [
                    'todayBtn' => true,
                    'format' => 'dd-mm-yyyy',
                    'language' => 'ru'
                ],
            ],
            [
                'label'                 => 'Действует до',
                'name'                  => 'expired_at',
                'type'                  => 'date_picker',
                'date_picker_options'   => [
                    'todayBtn' => true,
                    'format' => 'dd-mm-yyyy',
                    'language' => 'ru'
                ],
            ],
            [
                'label'     => 'Статус активности',
                'name'      => 'active',
                'type'      => 'select_from_array',
                'options'   => [0 => 'Выключен', 1 => 'Включен'],
                'default'   => 1
            ],
            [
                'label'     => 'Статус использования',
                'name'      => 'used',
                'type'      => 'select_from_array',
                'options'   => [0 => 'Закончился', 1 => 'Активен'],
                'default'   => 1
            ],
            [
                'label'                 => 'Скидка на производителей',
                'name'                  => 'manufacturer',
                'type'                  => 'select2_from_ajax_multiple',
                'entity'                => 'manufacturer',
                'attribute'             => 'title',
                'model'                 => 'App\Models\Manufacturer',
                'pivot'                 => true,
                'data_source'           => url('api/manufacturer'),
                'placeholder'           => 'Производитель',
                'minimum_input_length'  => 3,
                'translatable'          => true,
                'method'                => 'post'
            ],
            [
                'label'                 => 'Скидка на категории',
                'name'                  => 'category',
                'type'                  => 'select2_from_ajax_multiple_custom',
                'entity'                => 'category',
                'attribute'             => 'title',
                'model'                 => 'App\Models\CategoryDescription',
                'pivot'                 => true,
                'data_source'           => url('api/category'),
                'placeholder'           => 'Категория',
                'minimum_input_length'  => 3,
                'translatable'          => true,
                'method'                => 'post'
            ],
            [
                'label'                 => 'Скидка на продукты',
                'name'                  => 'product',
                'type'                  => 'select2_from_ajax_multiple_custom',
                'entity'                => 'product',
                'attribute'             => 'title',
                'model'                 => 'App\Models\ProductDescription',
                'pivot'                 => true,
                'data_source'           => url('api/product'),
                'placeholder'           => 'Товар',
                'minimum_input_length'  => 3,
                'translatable'          => true,
                'method'                => 'post'
            ]
        ]);

        // add asterisk for fields that are required in PromoRequest
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
