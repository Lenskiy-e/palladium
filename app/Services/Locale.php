<?php


namespace App\Services;


class Locale
{
    /**
     * Возвращает локаль
     * @return string
     */
    public static function getLocale() : string
    {
        $url = explode('/', request()->path());

        $locales = array_keys(config('app.locales'));

        $default_locale = config('app.fallback_locale');

        /*
         * Если локаль в списке
         */
        if(in_array($url[0], $locales))
        {
            app()->setLocale($url[0]);
            return $url[0];
        }

        app()->setLocale($default_locale);
        return '';
    }

    /**
     * Возвращает все локали в ввиде массива
     * key - ключ локали
     * title - название локали
     * href - ссылка на текущую страницу с локалью
     * @return array
     */
    public static function getLocales() : array
    {
        $locale = app()->getLocale();
        $locales = config('app.locales');
        $prefix = request()->route()->getPrefix();
        $url = request()->path();
        if($prefix)
        {
            $url = preg_replace("~{$locale}\/~", '', $url);
        }
        $result = [];

        foreach ($locales as $key => $name)
        {
            $result[] = [
                'key'       => $key,
                'title'     => $name,
                'href'      => $key === config('app.fallback_locale') ? url($url) : url($key . '/' . $url),
            ];
        }

        return $result;
    }
}
