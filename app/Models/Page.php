<?php

namespace App\Models;

use Backpack\PageManager\app\Models\Page as Base;

class Page extends Base
{
    protected $fillable = ['template', 'name', 'title', 'slug', 'content', 'extras', 'footer_pointer', 'header_pointer'];
}
