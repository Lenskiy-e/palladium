<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\AttributesRequest as StoreRequest;
use App\Http\Requests\AttributesRequest as UpdateRequest;
use App\Models\Parameter;
use Illuminate\Http\Request;

/**
 * Class AttributesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class AttributesCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Attributes');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/attributes');
        $this->crud->setEntityNameStrings('Attributes', 'Спецификация');
      //  $this->crud->model->translatable(['title','parameters']);
        // var_dump(get_class_methods($this->crud->model));
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $table = [
            'name' => 'parameters',
            'label' => 'Опции',
            'type' => 'parameters_table',
            'entity_singular' => 'parameter',
            'columns' => [
                'ru' => 'Название',
                'ua' => 'Назва'
            ]
        ];
        $attribute_parameters = Parameter::where('attribute_id',$this->crud->getCurrentEntryId())->get();
        if(!$attribute_parameters->isEmpty()){
          $names = array();
          $i = 0;
          foreach ($attribute_parameters as $att) {
            $names[] = array_merge(json_decode($att->title, true), ['img' => url($att->image)], ['id' => $att->id]);
            $i++;
          }
          $table = array_merge($table, ['value' => json_encode($names, true)]);
        }

        $this->crud->addfields([
          [
            'label' => 'Название спецификации',
            'type' => 'text',
            'name' => 'title',
          ],
          [
            'label' => 'Суфикс',
            'type'  =>'text',
            'name'  => 'sufix'
          ],
          [
            'label' => 'Префикс',
            'type'  =>'text',
            'name'  => 'prefix'
          ],
          [
            'label' => 'Множественный выбор',
            'type' => 'checkbox',
            'name' => 'multiply',
          ],
          $table
        ]);

        $this->crud->addColumns([
          [
            'name' => 'title',
            'label' => 'Параметр'
          ],
          [
            'name' => 'sufix',
            'label' => 'Суфикс'
          ],
          [
            'name' => 'prefix',
            'label' => 'Префикс'
          ],
        ]);



        // TODO: remove setFromDb() and manually define Fields and Columns
        //$this->crud->setFromDb();

        // add asterisk for fields that are required in AttributesRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        //Создаем параметры атрибута

        $request->merge([ 'id' => $this->crud->entry->id]);
        $this->crud->model->editParameters($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {

        $redirect_location = parent::updateCrud($request);
        $this->crud->model->editParameters($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
