<?php

namespace App\Services\Breadcrumbs;


use App\Http\Interfaces\BreadcrumbsInterface;
use Illuminate\Support\Facades\Lang;

class OrderBreadcrumbs implements BreadcrumbsInterface
{
    /**
     * Формирование массива крошек
     * для категории
     * @param int $id
     * @return array
     */
    public function generate(int $id): array
    {
        $result = [
          'title'   => Lang::get('common.order-breadcrumb'),
          'href'    => ''
        ];

        return $result;
    }
}
