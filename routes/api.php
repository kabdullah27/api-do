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

Route::post('login', 'APIController@login');
Route::post('register', 'APIController@register');

Route::group(['middleware' => 'auth.jwt'], function () {
    Route::get('logout', 'APIController@logout');
    Route::get('user', 'APIController@getAuthenticatedUser');

    Route::prefix('do')->group(function () {
        Route::get('show', 'DO_Controller@show');
        Route::post('insert', 'DO_Controller@store');
    });

    Route::prefix('item')->group(function () {
        Route::get('show', 'Item_controller@show');
        Route::post('insert', 'Item_controller@store');
        Route::post('update', 'Item_controller@update');
        Route::post('delete', 'Item_controller@destroy');
    });

    Route::prefix('cust')->group(function () {
        Route::get('show', 'Customer_controller@show');
        Route::post('insert', 'Customer_controller@store');
        Route::post('update', 'Customer_controller@update');
        Route::post('delete', 'Customer_controller@destroy');
    });
});
