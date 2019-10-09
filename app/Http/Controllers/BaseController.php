<?php

namespace App\Http\Controllers;

use App\Models\CategoryDescription;
use App\Models\Page;
use App\Models\Setting;
use App\Services\Category;
use App\Services\Errors;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\View;

abstract class BaseController extends Controller
{
    /**
     * @var Setting|null
     */
    protected $common_data;

    /**
     * BaseController constructor.
     * @param null $common_data
     */
    public function __construct($common_data = null)
    {
        try
        {
            $this->common_data = $common_data;
            if(!$common_data)
            {
                $this->common_data = $this->getSettings();
            }

            View::share([
                'common_data'   => $this->common_data,
                'footer_links'  => $this->getLinks('footer'),
                'header_links'  => $this->getLinks('header'),
                'categories'    => $this->getCategories(),
            ]);
        }catch(\Throwable $th)
        {
            $errors = new Errors($th);
            $errors->staticError();
        }

    }

    /**
     * @return Setting|null
     */
    protected function getSettings() : ? Setting
    {
        try
        {
            $setting = new Setting();

            $data = $setting->firstOrFail();

            return $data;
        }catch(ModelNotFoundException $th)
        {
            echo <<<HERE
Не заполнены поля сайта
Выполните в терминле
php artisan db:seed
HERE;
            exit();
        }
    }

    /**
     * @param string $type
     * @return array
     */
    protected function getLinks($type = '') : array
    {
        try{
            $pages = new Page();

            $footer_links = $pages->select('name', 'slug')->where('footer_pointer', 1)->get()->toArray();

            $header_links = $pages->select('name', 'slug')->where('header_pointer', 1)->get()->toArray();

            $data =
                [
                'footer'        => $footer_links,
                'header'        => $header_links,
                ];

            if($type && array_key_exists($type, $data))
            {
                return $data[$type];
            }

            return $data;
        }catch (ModelNotFoundException $th)
        {
            return [
                'footer' => [],
                'header' => []
            ];
        }
    }

    /**
     * @return array
     * Получение массива категорий
     * первых трех уровней
     */
    protected function getCategories() : array
    {
        $result = [];

        $category_description_instance = new CategoryDescription();

        $category_service = new Category();

        $main_categories = $category_description_instance->where('depth',1)->whereHas('category', function($query){
            $query->where('main', 1);
        })->get();

        foreach ($main_categories as $category)
        {
            $result = $category_service->getCategoryTree($category);
        }

        return $result;
    }
}
