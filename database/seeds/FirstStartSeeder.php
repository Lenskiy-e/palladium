<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FirstStartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Добавляем админа

        DB::table('users')->insert([
            'name'      => 'Admin (Удалить после создания первого пользователя!)',
            'email'     => 'admin@admin.com',
            'password'  =>  bcrypt('password'),
            'is_admin'  => '1'
        ]);

        // Создаем роль админа

        DB::table('roles')->insert(
            [
                'name'       => 'admin',
                'guard_name' => 'backpack',
            ]
                
        );

        // Наполняем разрешения

        DB::table('permissions')->insert([
            [
                'name'       => 'appoint user to product',
                'guard_name' => 'backpack',
            ],
            [
                'name'       => 'edit product',
                'guard_name' => 'backpack',
            ],
            [
                'name'       => 'publish',
                'guard_name' => 'backpack',
            ],
            [
                'name'       => 'show log',
                'guard_name' => 'backpack',
            ]   
        ]);

        // Создаем прайс агрегаторы

        DB::table('aggregators')->insert([
            [
                'status'   => 0,
                'slug'     => 'hotline',
                'name'     => 'hotline',
                'link'     =>  url('/feed/' . 'hotline'),
                'template' => 'hotline'
            ],
            [
                'status'   => 0,
                'slug'     => 'price',
                'name'     => 'price',
                'link'     =>  url('/feed/' . 'price'),
                'template' => 'price'
            ],
            [
                'status'   => 0,
                'slug'     => 'nadavi',
                'name'     => 'Надави',
                'link'     =>  url('/feed/' . 'nadavi'),
                'template' => 'nadavi'
            ],
            [
                'status'   => 0,
                'slug'     => 'tekhnoportal',
                'name'     => 'Технопортал',
                'link'     =>  url('/feed/' . 'tekhnoportal'),
                'template' => 'yandex_description'
            ],
            [
                'status'   => 0,
                'slug'     => 'yandex',
                'name'     => 'Yandex',
                'link'     =>  url('/feed/' . 'yandex'),
                'template' => 'yandex'
            ],
            [
                'status'   => 0,
                'slug'     => 'yandex_short',
                'name'     => 'Yandex short',
                'link'     =>  url('/feed/' . 'yandex_short'),
                'template' => 'yandex_short'
            ],
            [
                'status'   => 0,
                'slug'     => 'yandex_description',
                'name'     => 'Yandex description',
                'link'     =>  url('/feed/' . 'yandex_description'),
                'template' => 'yandex_description'
            ],
            [
                'status'   => 0,
                'slug'     => 'google_pokupki',
                'name'     => 'Google покупки',
                'link'     =>  url('/feed/' . 'google_pokupki'),
                'template' => 'google_pokupki'
            ],
            [
                'status'   => 0,
                'slug'     => 'facebook',
                'name'     => 'Facebook',
                'link'     =>  url('/feed/' . 'facebook'),
                'template' => 'facebook'
            ],
            [
                'status'   => 0,
                'slug'     => 'prom',
                'name'     => 'Prom',
                'link'     =>  url('/feed/' . 'prom'),
                'template' => 'yandex_description'
            ]
        ]);

        DB::table('settings')->insert(
            [
                'id'                => 0,
                'footer_template'   => 'footer'
            ]
        );
    }
}
