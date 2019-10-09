<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\BaseController;
use App\Services\Errors;
use Backpack\PageManager\app\Models\Page;

class PageController extends BaseController
{
    public function index($slug, $subs = null)
    {
        try
        {
            $page = Page::findBySlug($slug);

            if (!$page)
            {
                abort(404, 'Please go back to our <a href="'.url('').'">homepage</a>.');
            }

            $this->data['title'] = $page->title;
            $this->data['page'] = $page->withFakes();

            return view('front.pages.'.$page->template, $this->data);
        }catch(\Throwable $th)
        {
            $error = new Errors($th);
            return $error->staticError();
        }
    }
}
