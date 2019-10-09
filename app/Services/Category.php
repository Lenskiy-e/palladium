<?php


namespace App\Services;

use App\Models\CategoryDescription;

class Category
{
    /**
     * @var array
     */
    private $tree = [];

    /**
     * Получение хлебных крошек к категории
     * @param CategoryDescription $category
     * @return array
     * TODO переписать в breadcrumb service
     */
    public function breadcrumbs(CategoryDescription $category) : array
    {
        $breadcrumbs[] = [
            'name' => $category->title,
            'href' => url($category->url->url)
        ];

        while ($category->parent_id !== 0)
        {
            $category = $category->parent()->get()->first();
            array_unshift($breadcrumbs, ['name' => $category->title, 'href' => url($category->url->url)]);
        }
        array_unshift($breadcrumbs, ['name' => 'Главная', 'href' => url('/')]);

        return $breadcrumbs;
    }


    /**
     * @param CategoryDescription $category
     * @return array
     * формирование первых трех уровней категорий
     */
    public function getCategoryTree(CategoryDescription $category) : array
    {
        try
        {
            $data = [
                'title' => $category->title,
                'href'  => $category->url->url,
                'icon'  => $category->category->icon
            ];

            if($category->parent)
            {
                $this->tree[$category->depth][$category->parent->id][$category->id] = $data;
            }else
            {
                $this->tree[$category->depth][$category->id] = $data;
            }

            if($category->children->count())
            {
                foreach ($category->children as $childred)
                {
                    $this->getCategoryTree($childred);
                }
            }

            return $this->tree;
        }catch (\Throwable $th)
        {
            new Errors($th);
            return $this->tree;
        }
    }

}
