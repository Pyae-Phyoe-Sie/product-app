<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


// login
Route::post('login', 'API\UserController@login');

// register
Route::post('register', 'API\UserController@register');


Route::group(['middleware' => 'auth:api'], function(){

    // get a ticket information
    Route::get('user/{id}', 'API\UserController@getUser');

    // get a ticket information
    Route::get('get/category/{id}', 'API\CategoryController@index');

    // create ticket
    Route::post('create/category', 'API\CategoryController@store');

    // update ticket
    Route::post('update/category', 'API\CategoryController@update');

    // delete ticket
    Route::post('delete/category/{id}', 'API\CategoryController@deleteCategory');


    // get a ticket information
    Route::get('get/product/{id}', 'API\ProductController@index');

    // create ticket
    Route::post('create/product', 'API\ProductController@store');

    // update ticket
    Route::post('update/product', 'API\ProductController@update');

    // delete ticket
    Route::post('delete/product/{id}', 'API\ProductController@deleteProduct');
});