<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {return view('welcome');});

// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');

//Local routers
Route::match(['get','post'],'register','WebController@register');
Route::match(['get','post'],'pinset','WebController@pinset');
Route::match(['get','post'],'authenticate','WebController@authenticate');
Route::match(['get','post'],'info','WebController@index');
Route::controller('product','Products\ProductController');
Route::controller('pif','Products\PifController');
/*
Route::match(['get','post'],'/pif', function ($act='main') {
    if($act=="register"){
        //route('authenticate');
        redirect()->route('/client.authenticate');
        //return view('auth');
    }
    return view('pif',['action'=>$act]);
});

//Route::resource('client','ClientController');

Route::match(['get','post'],'/client',[
  'as'=>'client'
  ,'middleware'=>'auth'
  ,'uses'=>'ClientController@showProfile'
]);
*/
