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

Route::any('/message','Message\MessageController@index');

Route::get('/sendmessage','Message\MessageController@SendMessage');
Route::get('/sendcarmessage','Message\MessageController@CarMessage');

//测试
Route::get('/message/temp','Message\MessageController@Temp');

Route::get('/temp','Test\TestController@temp');