<?php


namespace App;


use App\Services\Filter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class FilterBuilder
{

    /**
     * Фильтрация
     * @param String $type
     * @param Model $model
     * @param Request $data
     * @return mixed
     */
    public function build(String $type, Model $model, Request $data)
    {
        $type = $this->getFilter($type);

        $filter = new $type( new Filter($data) );

        return $filter->generate($model, $data);
    }

    /**
     * Получение названия фильтра
     * @param String $type
     * @return string
     */
    private function getFilter(String $type) : string
    {
        return env("FILTERS_DIR") . mb_strtolower($type) . 'Filter';
    }
}
