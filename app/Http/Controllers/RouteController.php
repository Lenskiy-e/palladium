<?php

namespace App\Http\Controllers;
use App\Models\Url;
use App\Services\Errors;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Interfaces\FrontInterface;
use App\Http\Interfaces\FilteredFrontInterface;
use Log;
use Symfony\Component\HttpFoundation\Response;

class RouteController extends Controller
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * RouteController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $route
     * @param int|null $page
     * @return \Symfony\Component\HttpFoundation\Response
     * Роутинг
     */
    public function route(string $route, int $page = null)
    {
        try
        {
            /**
             * Проверяем, используется ли фильтр
             */

            $filter_check_pattern = "~([\da-z-]+)\/filter\/([\da-z-]+)\/params\/(([\d-]+)?([\d]))$~";

            // если используется
            if (preg_match($filter_check_pattern, $route, $filter)) {
                // какой контроллер
                $instance_url = $filter[1];

                // сеошный урл фильта
                $seo_filter = $filter[2];

                // параметры фильтра
                $parameter_ids = $filter[3];

                return $this->staticFilter($instance_url, $seo_filter, $parameter_ids, $page);
            }

            // Получение данных по урлу
            $url_data = $this->getInstanceByUrl($route);

            // Получение контроллера
            $controller = $this->getController($url_data->object_type);

            /**
             * Вызываем метод show нужного контроллера
             * и передаем ему нужный id
             */
            return $this->startWithoutFilter($controller, $url_data->object_id, $page);
        }
        catch(ModelNotFoundException $th)
        {
            abort(404);
        }
        catch (\Throwable $th) {
            $error = new Errors($th);
            return $error->staticError();
        }

    }


    /**
     * @param string $instance_url
     * @param string $seo_filter
     * @param string $parameter_ids
     * @param int|null $page
     * @return \Symfony\Component\HttpFoundation\Response
     * Работа с сео урлами фильтра
     */
    public function staticFilter(string $instance_url, string $seo_filter, string $parameter_ids, int $page = null) : Response
    {

        try
        {
            // получение данных по урлу
            $instance = $this->getInstanceByUrl($instance_url);

            // получение контроллера
            $controller = $this->getController($instance->object_type);

            $data = collect([
                'parameter_ids' => $parameter_ids,
                'instance_id'   => $instance->object_id,
                'seo_filter'    => $seo_filter
            ]);

            // запуск
            return $this->startWithFilter($controller, $data, $page);
        }
        catch(ModelNotFoundException $th)
        {
            abort(404);
        }
        catch (\Throwable $th)
        {
            $error = new Errors($th);
            return $error->staticError();
        }
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * Работа с аяксовыми запросами фильтра
     */
    public function ajaxFilter(Request $request)
    {
        try
        {
            // получение данных по урлу
            $instance = $this->getInstanceByUrl($request->base_url);

            // получение контроллера
            $controller = $this->getController($instance->object_type);

            // запуск
            return $this->startAjaxFilter($controller, $request);
        }
        catch(ModelNotFoundException $th)
        {
            abort(404);
        }
        catch (\Throwable $th)
        {
            $error = new Errors($th);
            return $error->ajaxError();
        }
    }


    /**
     * @param string $url
     * @return mixed
     * Получение данных по урлу
     */
    protected function getInstanceByUrl(string $url)
    {
        $url_instance = new Url();
        /**
         * проверяем, есть ли в урле .htlm
         * если нету - добавляем
         */
        if(!preg_match("~.*\.html~", $url))
        {
            $url .= '.html';
        }

        return $url_instance->where('url', mb_strtolower($url))->firstOrFail();
    }


    /**
     * @param string $type
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     * @throws \Exception
     * Получение данных по урлу
     */
    protected function getController(string $type)
    {
        $controller = app(env("CONTROLLERS_DIR") . ucfirst($type) . 'Controller');
        if(!$controller)
        {
            throw new ModelNotFoundException('Controller not found');
        }
        return $controller;
    }


    /**
     * @param FrontInterface $controller
     * @param int $id
     * @param int|null $page
     * @return mixed
     * Вызов метода show контроллера без фильтрации
     */
    protected function startWithoutFilter(FrontInterface $controller, int $id, int $page = null)
    {

        return $controller->show($id, $page);
    }

    /**
     * @param FilteredFrontInterface $controller
     * @param $data
     * @param int|null $page
     * @return mixed
     * Вызов метода filter контроллера с фильтром
     */
    protected function startWithFilter(FilteredFrontInterface $controller, $data, int $page = null)
    {
        return $controller->filter($data, $page);
    }

    /**
     * @param FilteredFrontInterface $controller
     * @param Request $request
     * @return mixed
     * Вызов метода ajaxFilter контроллера с фильтром
     */
    protected function startAjaxFilter(FilteredFrontInterface $controller, Request $request)
    {
        return $controller->ajaxFilter($request);
    }

}
