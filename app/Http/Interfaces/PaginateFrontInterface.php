<?php
namespace App\Http\Interfaces;

use App\Http\Interfaces\FrontInterface;

interface PaginateFrontInterface extends FrontInterface
{
    public function show(int $id, int $page = null);
}

