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



Route::any('/jianpiao', 'JianPiao\JianPiaoController@index');

Route::any('/cardquery', 'Card\CardQueryController@index');

Route::any('/message','Message\MessageController@index');

Route::get('/sendrevenuemessage','Message\MessageController@SendMessage');

Route::any('/sendmessage','SendMessage\SendMessageController@index');
Route::get('/sendcarmessage','SendMessage\SendMessageController@SendCarMessage');


Route::any('/query','Query\QueryController@index');

//测试
Route::get('/message/temp','Message\MessageController@Temp');

Route::get('/temp','Test\TestController@temp');

Route::any('/test', 'Test\TestController@index');

Route::any('/test/testquery', 'Test\TestController@testquery');

Route::get('/test/test1', 'Test\TestController@test1');