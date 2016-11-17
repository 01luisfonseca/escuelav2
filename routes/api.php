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

Route::group(['middleware' => ['auth:api','permited'],'namespace'=>'Api'], function () {
	Route::get('/user', function (Request $request) {
    	return $request->user(); // Info del usuario logueado
	});
	
	/** RUTAS EMPLEADOS TABLE **/
	Route::group(['prefix'=>'empleados','middleware'=>'admin'],function(){
		//Basicos
		Route::get('/','EmpleadosCtrl@index');
		Route::post('/','EmpleadosCtrl@store');
		Route::get('/{id}','EmpleadosCtrl@show');
		Route::put('/{id}','EmpleadosCtrl@update');
		Route::delete('/{id}','EmpleadosCtrl@destroy');
		//Adicionales
		Route::get('/search/{info}','EmpleadosCtrl@search');
		Route::get('/range/{ini}','EmpleadosCtrl@index');
	});

	/** RUTAS GENERALES TABLE **/
	Route::group(['prefix'=>'generales','middleware'=>'admin'],function(){
		//Basicos
		Route::get('/','GenCtrl@index');
		Route::post('/','GenCtrl@store');
		Route::get('/{id}','GenCtrl@show');
		Route::put('/{id}','GenCtrl@update');
		Route::delete('/{id}','GenCtrl@destroy');
		//Adicionales
	});

	/** RUTAS USERS TABLE **/
	Route::group(['prefix'=>'users','middleware'=>'admin'],function(){
		//Basicos
		Route::get('/','UserCtrl@index');
		Route::post('/','UserCtrl@store');
		Route::get('/{user}','UserCtrl@show');
		Route::put('/{user}','UserCtrl@update');
		Route::delete('/{user}','UserCtrl@destroy');
		//Adicionales
		Route::get('/search/{info}','UserCtrl@search');
		Route::get('/range/{ini}','UserCtrl@index');
		Route::put('/status/{user}/{status}','UserCtrl@modEstado');
		Route::get('/add/empleables','UserCtrl@userEmpleable');
	});

	/** RUTAS PERFIL TABLE **/
	Route::group(['prefix'=>'perfil'],function(){
		//Basicos
		Route::get('/','PerfilCtrl@show');
		Route::put('/','PerfilCtrl@update');
		//Adicionales
	});

	/** RUTAS TIPO_USUARIO TABLE **/
	Route::group(['prefix'=>'tipo','middleware'=>'admin'],function(){
		Route::post('/','TipoCtrl@store');
		Route::put('/{id}','TipoCtrl@update');
		Route::delete('/{id}','TipoCtrl@destroy');
		Route::get('/','TipoCtrl@index');
		Route::get('/{id}','TipoCtrl@show');
	});
});
