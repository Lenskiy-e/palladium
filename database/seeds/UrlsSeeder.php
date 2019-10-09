<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\DB;

class UrlsSeeder extends Seeder
{
    public function run(Faker $faker)
    {
        DB::table('urls')->insert([
            'url' => $faker->string,
            'object_id' => random_int(15,200000),
            'object_type' => 'product'
        ]);
    }
}
