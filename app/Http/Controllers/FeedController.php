<?php

namespace App\Http\Controllers;

use App\Services\Errors;
use App\Models\Feed;

class FeedController extends Controller
{
    public function index(string $type)
    {
        try
        {
            $aggregator = new Feed();
            return $aggregator->generate($type);
        }catch (\Throwable $th)
        {
            $error = new Errors($th);
            return $error->staticError();
        }
    }
}
