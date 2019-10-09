<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\BaseController;
use App\Services\Errors;

class HomeController extends BaseController
{

    public function index(){
        try
        {
            return view('front.home');
        }catch(\Throwable $th)
        {
            $error = new Errors($th);
            return $error->staticError();
        }
    }
}
