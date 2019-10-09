<?php
namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\AccessoriesRequest as StoreRequest;
use App\Http\Requests\AccessoriesRequest as UpdateRequest;

/**
 * Class AccessoriesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class AccessoriesCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Accessories');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/accessories');
        $this->crud->setEntityNameStrings('accessories', 'Аксессуары');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
       // $this->crud->setFromDb();

        $this->crud->addFields([
            [           // 1-n relationship
                'label'                => "Категория аксессуар", // Table column heading
                'type'                 => "select2_from_ajax_custom",
                'name'                 => 'category_id', // the column that contains the ID of that connected entity
                'entity'               => 'category', // the method that defines the relationship in your Model
                'translatable'         => true, // если attribute переводится
                'attribute'            => "title", // foreign key attribute that is shown to user
                'model'                => "App\Models\CategoryDescription", // foreign key model
                'data_source'          => url("api/category"), // url to controller search function (with /{id} should return model)
                'placeholder'          => "Выберите категорию", // placeholder for the select
                'minimum_input_length' => 2, // minimum characters to type before querying results
                'method'               =>'post'
            ],
            [
                'label'                => "Продукт аксессуар", 
                'type'                 => "select2_from_ajax_custom",
                'name'                 => 'product_id', 
                'entity'               => 'product', 
                'translatable'         => true, // если attribute переводится
                'attribute'            => "title", 
                'model'                => "App\Models\ProductDescription",
                'data_source'          => url("api/product"), 
                'placeholder'          => "Выберите категорию", 
                'minimum_input_length' => 2,
                'method'               =>'post'
            ],
            [
                'label'                => "Параметр аксессуара", 
                'type'                 => "select2_from_ajax_custom",
                'name'                 => 'parameter_id', 
                'entity'               => 'parameter', 
                'translatable'         => true, // если attribute переводится
                'attribute'            => "title", 
                'model'                => "App\Models\Parameter",
                'data_source'          => url("api/parameter"), 
                'placeholder'          => "Выберите параметр", 
                'minimum_input_length' => 1,
                'method'               =>'post'
            ],
            [
                'label'                => '<hr>Относится к категориям',
                'type'                 => "select2_from_ajax_multiple_custom",
                'translatable'         => true, // если attribute переводится
                'name'                 => 'accessoryToCategory',
                'entity'               => 'accessoryToCategory',
                'model'               => 'App\Models\CategoryDescription',
                'attribute'            => 'title',
                'data_source'          => url("api/category"),
                'placeholder'          => "Выберите категории",
                'minimum_input_length' => 2,
                'pivot'                => true,
                'method'               =>'post'
            ],
            [
                'label'                => 'Относится к товарам',
                'type'                 => "select2_from_ajax_multiple_custom",
                'translatable'         => true, // если attribute переводится
                'name'                 => 'accessoryToProducts',
                'entity'               => 'accessoryToProducts',
                'model'               => 'App\Models\ProductDescription',
                'attribute'            => 'title',
                'data_source'          => url("api/product"),
                'placeholder'          => "Выберите продукты",
                'minimum_input_length' => 2,
                'pivot'                => true,
                'method'               =>'post'
            ],
            [
                'label'                => 'Относится к параметрам',
                'type'                 => "select2_from_ajax_multiple_custom",
                'translatable'         => true, // если attribute переводится
                'name'                 => 'accessoryToParameters',
                'entity'               => 'accessoryToParameters',
                'model'                => 'App\Models\SearchableParameter',
                'attribute'            => 'title',
                'data_source'          => url("api/parameter"),
                'placeholder'          => "Выберите параметры",
                'minimum_input_length' => 1,
                'pivot'                => true,
                'method'               =>'post'
            ],
            // только для видимости
            [
                'name'       => 'name',
                'label'      => 'Название',
                'type'       => 'text',
                'attributes' => [
                    'readonly' => 'readonly'
                ]
            ]
             
        ]);

        $this->crud->addColumn([
            'label' => 'Название',
            'type'  => 'text',
            'name'  => 'name'
        ]);

        // add asterisk for fields that are required in AccessoriesRequest
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
