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
    return view('welcome');
});

Route::any('/test', 'Test\TestController@index');


Route::any('/jianpiao', 'JianPiao\JianPiaoController@index');

Route::any('/cardquery', 'Card\CardQueryController@index');

Route::get('/temp','TestController@temp');

Route::get('/message','Message\MessageController@index');

Route::get('/sendmessage','Message\MessageController@SendMessage');