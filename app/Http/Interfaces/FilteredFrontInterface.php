<?php
namespace App\Http\Interfaces;

use Illuminate\Http\Request;

interface FilteredFrontInterface
{
    public function filter($data, int $page = null);

    public function ajaxFilter(Request $request);
}

