<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\AdminOrderRequest as StoreRequest;
use App\Http\Requests\AdminOrderRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;
use App\Models\Product;

/**
 * Class OrdersCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class OrdersCrudController extends CrudController
{
    private $status_list;
    public function setup()
    {
        $this->status_list = [
            0 => 'Не оформлен',
            1 => 'Оформлен',
            2 => 'Отправлен на доставку',
            3 => 'Отменен',
            4 => 'Доставлен',
        ];
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Order');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/orders');
        $this->crud->setEntityNameStrings('orders', 'orders');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $order = $this->crud->getCurrentEntry();

        // TODO: remove setFromDb() and manually define Fields and Columns
        //$this->crud->setFromDb();
        if($order)
        {
            $this->generateFields($order);
        }

        $this->crud->addColumns([
            [
                'label' => '№ заказа',
                'type' => 'number',
                'name' => 'id'
            ],
            [
                'label' => 'Имя клиента',
                'type' => 'text',
                'name' => 'user.name',
            ],
            [
                'label' => 'Фамилия клиента',
                'type' => 'text',
                'name' => 'user.profile.last_name',
            ],
            [
                'label' => 'Сумма',
                'type' => 'number',
                'suffix' => ' грн.',
                'name' => 'total_amount'
            ]
        ]);

        // Фильтры
        $this->addFilters();

        // add asterisk for fields that are required in OrdersRequest
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

    public function addFilters()
    {
        $this->crud->addFilter(
            [
            'name'  => 'complete',
            'label' => 'Статус',
            'type'  => 'select2',
            ],
            function()
            {
                return $this->status_list;
            },
            function($value)
            {
               $this->crud->addClause('where', 'complete', $value);
            }
        );

        $this->crud->addFilter(
            [
                'name'          => 'user_id',
                'label'         => 'Поиск по фамилии',
                'type'          => 'select2_ajax',
                'placeholder'   => 'Введите фамилию'
            ],
            url('order/user'),
            function($value)
            {
                $this->crud->addClause('where', 'user_id', $value);
            }
        );

        $this->crud->addFilter(
            [
                'name'          => 'phone',
                'label'         => 'Поиск по телефону',
                'type'          => 'select2_ajax',
                'placeholder'   => 'Введите телефон'
            ],
            url('order/phone'),
            function($value)
            {
                $this->crud->addClause('where', 'user_id', $value);
            }
        );
    }

    protected function generateFields($order)
    {

        /*
        * Общая информация о заказе
        */

        $common_fields = [
            [
                'name'          => 'id',
                'label'         => 'Номер заказа',
                'type'          => 'number',
                'attributes'    => ['readonly' => 'readonly'],
                'tab'           => 'Общие данные'
            ],
            [
                'name'          => 'total_count',
                'label'         => 'Общее кол-во товаров',
                'type'          => 'number',
                'attributes'    => ['readonly' => 'readonly'],
                'tab'           => 'Общие данные'
            ],
            [
                'name'          => 'total_amount',
                'label'         => 'Сумма заказа',
                'type'          => 'number',
                'suffix'         => ' грн.',
                'attributes'    => ['readonly' => 'readonly'],
                'tab'           => 'Общие данные'
            ],
            [
                'name'          => 'complete',
                'type'          => 'select_from_array',
                'label'         => 'Статус',
                'options'       => $this->status_list,
                'allows_null'   => false,
                'tab'           => 'Общие данные'
            ],
            [
                'name'          => 'promocode',
                'type'          => 'text',
                'label'         => 'Промокод',
                'tab'           => 'Общие данные',
                'value'         => $order->promocode->code ?? '',
                'attributes'    => ['readonly' => 'readonly'],
            ]
        ];

        /*
         * Информция о продуктах в заказе
         */
        $products = $order->product;

        $order_products = [];
        foreach ($products as $product)
        {
            $order_products[] = [
                'name'  => $product->title,
                'cost'  => $product->pivot->cost,
                'count' => $product->pivot->count
            ];
        }

        $products_fields = [
            [
                'name'              => 'products',
                'label'             => 'Продукты',
                'type'              => 'table',
                'entity_singulat'   => 'product',
                'columns'           => [
                    'name'  => 'Название',
                    'cost'  => 'Стоимость',
                    'count' => 'Количество'
                ],
                'tab'               => 'Продукты',
                'value'             => $order_products
            ]
        ];

       /*
        * Информация о клиенте
        */

        $user = $order->user()->first();

        $user_fields = [
            [
                'label'       => 'Id',
                'name'        => 'user_id',
                'type'        => 'hidden'
            ],
            [
                'label'       => 'Имя',
                'name'        => 'first_name',
                'type'        => 'text',
                'entity'      => 'user',
                'model'       => 'App\User',
                'value'       => $user->name,
                'attributes'  => ['readonly' => 'readonly'],
                'tab'         => 'Клиент'
            ],
            [
                'label'       => 'Фамилия',
                'name'        => 'last_name',
                'type'        => 'text',
                'entity'      => 'user',
                'model'       => 'App\User',
                'value'       => $user->profile->last_name,
                'attributes'  => ['readonly' => 'readonly'],
                'tab'         => 'Клиент'
            ],
            [
                'label'       => 'Отчество',
                'name'        => 'patronymic',
                'type'        => 'text',
                'entity'      => 'user',
                'model'       => 'App\User',
                'value'       => $user->profile->patronymic,
                'attributes'  => ['readonly' => 'readonly'],
                'tab'         => 'Клиент'
            ],
            [
                'label'       => 'Почта',
                'name'        => 'email',
                'type'        => 'email',
                'entity'      => 'user',
                'model'       => 'App\User',
                'value'       => $user->email,
                'attributes'  => ['readonly' => 'readonly'],
                'tab'         => 'Клиент'
            ],
            [
                'label'       => 'Почта',
                'name'        => 'client_phone',
                'type'        => 'text',
                'entity'      => 'user',
                'model'       => 'App\User',
                'value'       => $user->phone,
                'attributes'  => ['readonly' => 'readonly'],
                'tab'         => 'Клиент'
            ]
        ];

        $getter_fields = [
            [
                'label'       => 'Имя получателя',
                'name'        => 'getter_first_name',
                'type'        => 'text',
                'attributes'  => ['readonly' => 'readonly'],
                'tab'         => 'Получатель'
            ],
            [
                'label'       => 'Фамилия получателя',
                'name'        => 'getter_last_name',
                'type'        => 'text',
                'attributes'  => ['readonly' => 'readonly'],
                'tab'         => 'Получатель'
            ],
            [
                'label'       => 'Телефон получателя',
                'name'        => 'phone',
                'type'        => 'text',
                'attributes'  => ['readonly' => 'readonly'],
                'tab'         => 'Получатель'
            ]
        ];

        $this->crud->addFields($common_fields);
        $this->crud->addFields($getter_fields);
        $this->crud->addFields($user_fields);
        $this->crud->addFields($products_fields);
    }

}
