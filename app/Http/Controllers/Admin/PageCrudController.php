<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\PageManager\app\Http\Controllers\Admin\PageCrudController as Base;
use Backpack\CRUD\CrudPanel;

/**
 * Class PageCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class PageCrudController extends Base
{
    public function setup($template_name = false)
    {
        parent::setup($template_name);

        $this->crud->addFields([
            [
                'name'  => 'header_pointer',
                'type'  => 'checkbox',
                'label' => 'В шапку'
            ],
            [
                'name'  => 'footer_pointer',
                'type'  => 'checkbox',
                'label' => 'В подвал'
            ],
        ]);

        $this->crud->setModel('App\Models\Page');

    }
}
