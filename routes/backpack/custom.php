<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // fa-headphonescustom admin routes
    CRUD::resource('accessories', 'AccessoriesCrudController');
    CRUD::resource('aggregators', 'AggregatorsCrudController');
    CRUD::resource('attributes', 'AttributesCrudController');
    CRUD::resource('available_status', 'Available_statusCrudController');
    CRUD::resource('category', 'CategoryCrudController');
    CRUD::resource('categorydescription', 'CategoryDescriptionCrudController');
    CRUD::resource('categorycost', 'CategoryCostCrudController');
    CRUD::resource('clients','ClientsCrudController');
    CRUD::resource('content', 'ContentStatisticCrudController');
    CRUD::resource('hobbies','HobbiesCrudController');
    CRUD::resource('manufacturer', 'ManufacturerCrudController');
    CRUD::resource('orders','OrdersCrudController');
    CRUD::resource('page','PageCrudController');
    CRUD::resource('productdescription', 'ProductDescriptionCrudController');
    CRUD::resource('promo', 'PromoCrudController');
    CRUD::resource('sales','SalesCrudController');
    Crud::resource('setting','SettingCrudController');


    Route::post('productdescription/{id}/appoint', 'ProductDescriptionCrudController@appoint');
    Route::get('categorycost/{id}/show_log','CategoryCostCrudController@showLog');
    Route::get('categorydescription/{id}/show_log','CategoryDescriptionCrudController@showLog');
    Route::get('manufacturer/{id}/show_log','ManufacturerCrudController@showLog');
    Route::get('productsaggregat','AggregatorsCrudController@listproducts');
    Route::get('productdescription/{id}/show_log','ProductDescriptionCrudController@showLog');

});

/*
 * Autocomplete
 */

Route::get('api/product', 'App\Http\Controllers\AutocompleteController@searchProductsTable');
Route::post('api/product', 'App\Http\Controllers\AutocompleteController@searchProducts');
Route::post('api/manufacturer', 'App\Http\Controllers\AutocompleteController@searchManufacturer');
Route::get('api/product/{id}', 'App\Http\Controllers\AutocompleteController@showProduct');
Route::post('api/category', 'App\Http\Controllers\AutocompleteController@searchCategory');
Route::get('api/category/{id}', 'App\Http\Controllers\AutocompleteController@showCategory');
Route::post('api/parameter', 'App\Http\Controllers\AutocompleteController@searchParameter');
Route::get('category/name', 'App\Http\Controllers\AutocompleteController@getCategoryByTitle');


Route::post('api/type', 'App\Http\Controllers\Api\SaleController@getType');

// this should be the absolute last line of this file
