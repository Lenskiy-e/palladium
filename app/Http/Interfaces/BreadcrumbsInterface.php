<?php

namespace App\Http\Interfaces;

interface BreadcrumbsInterface
{
    /**
     * @param int $id
     * @return array
     */
    public function generate(int $id) : array;
}
