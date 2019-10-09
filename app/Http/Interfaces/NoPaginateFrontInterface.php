<?php
namespace App\Http\Interfaces;

interface NoPaginateFrontInterface extends FrontInterface
{
    public function show(int $id);
}

