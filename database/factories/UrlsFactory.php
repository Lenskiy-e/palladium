<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Models\Url::class, function (Faker $faker) {
    return [
        'object_type' => 'product',
        'url' => Str::slug($faker->name),
        'object_id' => random_int(15,2000000),
    ];
});
