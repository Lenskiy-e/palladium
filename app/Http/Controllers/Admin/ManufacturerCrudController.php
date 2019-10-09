<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\ManufacturerRequest as StoreRequest;
use App\Http\Requests\ManufacturerRequest as UpdateRequest;
use App\Models\Manufacturer;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use Helpers;

/**
 * Class ManufacturerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ManufacturerCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Manufacturer');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/manufacturer');
        $this->crud->setEntityNameStrings('manufacturer', 'manufacturers');

        $this->crud->logFrom = 'manufacturer';
        $this->crud->addButtonFromView('line','showLog','show_log','end');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        $this->crud->addFields
        ([
            [
                'label' => 'Название',
                'type'  => 'text',
                'name'  => 'title'
            ],
            [
                'label' => 'Описание',
                'type'  => 'summernote',
                'name'  => 'description'
            ],
            [
                'label' => 'Мета H1',
                'type'  => 'text',
                'name'  => 'h1'
            ],
            [
                'label' => 'Мета title',
                'type'  => 'text',
                'name'  => 'meta_title'
            ],
            [
                'label' => 'Мета description',
                'type'  => 'text',
                'name'  => 'meta_description'
            ],
            [
                'label' => 'Фото',
                'type'  => 'browse_custom',
                'name'  => 'image'
            ],
            [
                'label'   => 'Порядок сортировки',
                'type'    => 'number',
                'name'    => 'sort_order',
                'default' => 0
            ],
            [
                'label'   => 'Статус',
                'type'    => 'select_from_array',
                'name'    => 'active',
                'options' => [
                    0 => 'Выкл',
                    1 => 'Вкл'
                ]
            ],
            [
                'label'  => 'Поле object_type таблицы urls',
                'name'   => 'object_type',
                'type'   => 'hidden',
                'entity' => 'url',
                'model'  => 'App\Models\Url',
                'value'  => 'manufacturer'
            ],
            [
                'label'  => 'SEO Url товара',
                'name'   => 'url',
                'type'   => 'text',
                'entity' => 'url',
                'model'  => 'App\Models\Url',
            ],
        ]);

        $this->crud->getCurrentEntry();
        if($this->crud->entry && $this->crud->entry->url->url){
            $this->crud->addfield([
                'label'  => 'Ссылка',
                'name'   => 'href',
                'type'   => 'href',
                'value'  =>  url($this->crud->entry->url->url),
            ]);
        }

        $this->crud->addColumn([
            'label' => 'Название',
            'type' => 'text',
            'name' => 'title'
        ]);

        // add asterisk for fields that are required in ManufacturerRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        $request = generate_url($request);
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        $request = generate_url($request);
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function showLog($id){

        $manufacturer = Manufacturer::find($id);

        $data['from'] = Input::get('log_from');
        $data['to'] = Input::get('log_to');
        $data['translation'] = array();
        $data['rows'][] = $manufacturer->getLog($id,$data['from'],$data['to']);
        return view('data_log',compact('data'));
    }

}
