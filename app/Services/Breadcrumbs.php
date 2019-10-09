<?php
namespace App\Services;

use App\Services\Breadcrumbs\CategoryBreadcrumbs;
use App\Services\Breadcrumbs\ProductBreadcrumbs;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;

class Breadcrumbs
{
    /**
     * Фабрика хлебных крошек
     * @param string $type
     * @param int $id
     * @return string
     */
    public static function get(string $type, int $id) : string
    {
        $breadcrumbs[] = [
            'title'    => Lang::get('common.home-breadcrumb'),
            'href'      => url('/')
        ];
        $data = [];

        switch ($type)
        {
            case 'product':
                $service = new ProductBreadcrumbs();
                $data = $service->generate($id);
                break;
            case 'category':
                $service = new CategoryBreadcrumbs();
                $data = $service->generate($id);
                break;
        }
        return View::make('front.elements.breadcrumbs', ['breadcrumbs' => array_merge($breadcrumbs, $data)])->render();
    }
}
