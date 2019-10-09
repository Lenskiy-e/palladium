<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => \App\Services\Locale::getLocale()], function () {
    Auth::routes();
    Route::post('/temp', 'Auth\LoginController@tempCode');
    Route::post('/auth', 'Auth\LoginController@authenticate')->name('authenticate');
    Route::post('auth/view', 'Auth\LoginController@getView');

    Route::get('/', 'Front\HomeController@index');

    /**
     * Profile
     */

    Route::resource('profile', 'Front\ProfileController');

    /**
     * Order
     */

    Route::delete('order/product', 'Front\OrderController@deleteProduct');
    Route::post('order/ajax', 'Front\OrderController@ajaxInfo');
    Route::post('order/checkUser', 'Front\OrderController@checkUser');
    Route::get('order/user', 'AutocompleteController@getUserByLastName');
    Route::get('order/phone', 'AutocompleteController@getUserByPhone');
    Route::post('order/cart/{id}', 'Front\OrderController@cart');
    Route::post('order/promo', 'Front\OrderController@promo');

    Route::resource('order', 'Front\OrderController');


    Route::get('feed/{type}','FeedController@index');

    /*
     * Sales
     */

    Route::get('sales', 'Front\SalesController@all');
    Route::get('sales/{id}', 'Front\SalesController@single')->where(['id' => '[0-9]+']);

    /**
     * Router pages
     */

    Route::get('{instance}/filter/{url}/params/{ids}.html', 'RouteController@staticFilter')
        ->where('ids', '^([0-9-]+)?([0-9])$');

    Route::post('/filter/generate', 'RouteController@ajaxFilter');

    Route::post('/favorite', 'Front\ProductController@addToFavorite');
    Route::delete('/favorite', 'Front\ProductController@removeFavorites');

    /*
     * Dynamic routes
     */

    Route::get('/{route}/page/{number}.html', 'RouteController@route')->where(['number' => '[0-9]+','route' => '.*']);

    Route::get('/{route}', 'RouteController@route')->where(['route' => '.*\.html.*']);


    Route::get('{page}/{subs?}', ['uses' => 'Front\PageController@index'])
        ->where(['page' => '^(((?=(?!admin))(?=(?!\/)).))*$', 'subs' => '.*']);

    Route::get('glide/{path}', function($path){
        $server = \League\Glide\ServerFactory::create([
            'source' => app('filesystem')->disk('public')->getDriver(),
            'cache' => storage_path('glide'),
        ]);
        return $server->getImageResponse($path, Input::query());
    })->where('path', '.+');
});


