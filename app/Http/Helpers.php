<?php

use App\Models\CategoryDescription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

if (!function_exists('generate_url'))
{
    /**
     * Генератор SEO урлов
     * @param Request $request
     * @return Request
     */
    function generate_url(Request $request)
    {

        if(!$request->url)
        {
            $url = mb_strtolower(Str::slug($request->title)) . ".html";
            $request->merge(['url' => $url]);
        }else
        {
            if(!preg_match("~([a-zA-Z0-9\-]+)(\.html)$~", $request->url)){
                $new_url = preg_replace("~(.*)(\.html)~", "$1", $request->url);
                $new_url = preg_replace("~([^a-zA-Z0-9\-])~", "", $new_url) . '.html';
                $request->merge(['url' => $new_url]);
            }
        }
        return $request;
    }
}


if (!function_exists('get_current_url'))
{
    /**
     * Генератор урла для withPath пагинации
     * @param string $current_url
     * @return string|string[]|null
     */
    function get_current_url(string $current_url)
    {
        return preg_replace('~\.html~','', $current_url);
    }
}


if (!function_exists('check_current_page'))
{
    /**
     * Проверяет, не выходит ли текущая страница
     * за рамки интервала страниц пагинации
     * @param $obj
     * @param int $count
     * @param int $page
     * @param int $per_page
     * @param Request $request
     * @param string|null $url
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    function check_current_page($obj, int $count, int $per_page, Request $request, ?int $page = 1, string $url = null)
    {
        $redirect = $url ?? $obj->url->url;
        if( ceil($count / $per_page) < $page || ($page && $page < 2) )
        {
            if(!preg_match('~.html$~',$redirect))
            {
                $redirect .= '.html';
            }

            $gets = get_params_to_str($request);

            if($gets)
            {
                $redirect .= '?' . $gets;
            }

            return redirect($redirect);
        }
    }
}


if(!function_exists('str_for_url'))
{
    /**
     * Для форирования норм урла
     * @param string $str
     * @return string
     */
    function str_for_url(string $str) : string
    {
        return mb_strtolower(Str::slug($str));
    }
}


 if(!function_exists('get_params_to_str'))
 {
     /**
      * Формирование всех get
      * параметров в строку
      * @param Request $request
      * @param array $except
      * @return string
      */
     function get_params_to_str(Request $request, Array $except = ['page']) : string
     {
        $get_parameters = [];

        foreach ($request->except($except) as $key => $value) {
            $get_parameters[] = $key . '=' . $value;
        }

        return implode('&', $get_parameters);
     }
 }

 if(!function_exists('get_category_parents'))
 {
     /**
      * Форирование массива
      * категорий для крошек
      * @param int $id
      * @param array $breadcrumbs
      * @return array
      */
     function get_category_parents(int $id, array $breadcrumbs = []) : array
     {
        $category = CategoryDescription::with('parent')->findOrFail($id);

         array_unshift($breadcrumbs, [
             'title' => $category->title,
             'href'  => url($category->url->url)
         ]);

         $parent = $category->parent;
         if($parent)
         {
             return get_category_parents($parent->id, $breadcrumbs);
         }

         return $breadcrumbs;
     }
 }
