<?php

namespace App\Http\Controllers;

use App\Services\Errors;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends BaseController
{
    /**
     * HomeController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }

    /**
     * @return Response
     */
    public function index() : Response
    {
        try
        {
            return response()->view('home');
        }catch(\Throwable $th)
        {
            $error = new Errors($th);
            return $error->staticError();
        }
    }
}
