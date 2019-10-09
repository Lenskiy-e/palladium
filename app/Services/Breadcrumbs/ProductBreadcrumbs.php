<?php


namespace App\Services\Breadcrumbs;


use App\Http\Interfaces\BreadcrumbsInterface;
use App\Models\ProductDescription;

class ProductBreadcrumbs implements BreadcrumbsInterface
{
    /**
     * Формирование массива крошек
     * для товара
     * @param int $id
     * @return array
     */
    public function generate(int $id): array
    {
        $product = ProductDescription::findOrFail($id);

        $result = get_category_parents($product->mainCategory->id);

        $result[] = [
          'title'   => $product->title,
          'href'    => ''
        ];

        return $result;
    }
}
