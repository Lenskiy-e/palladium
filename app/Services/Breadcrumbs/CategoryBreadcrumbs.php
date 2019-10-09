<?php


namespace App\Services\Breadcrumbs;


use App\Http\Interfaces\BreadcrumbsInterface;
use App\Models\CategoryDescription;


class CategoryBreadcrumbs implements BreadcrumbsInterface
{
    /**
     * Формирование массива крошек
     * для категории
     * @param int $id
     * @return array
     */
    public function generate(int $id): array
    {
        $category = CategoryDescription::findOrFail($id);
        $result = [];

        if($category->parent)
        {
            $result = get_category_parents($category->parent->id);
        }


        $result[] = [
          'title'   => $category->title,
          'href'    => ''
        ];

        return $result;
    }
}
