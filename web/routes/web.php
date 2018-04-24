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

Route::get('/', function () {
    return view('main');
});
Route::get('/qr',"MainController@init" );
Route::get('/friends',"MainController@friends");
Route::get('/export',"MainController@export");
Route::get('/logout',"MainController@logout");
Route::get('/gen',"MainController@generate");
Route::get('/check',"MainController@check");
