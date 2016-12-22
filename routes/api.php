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

	/** RUTAS PAGO PENSION **/
	Route::group(['prefix'=>'pension','middleware'=>'coordinador'],function(){
		//Basicos
		Route::get('/','PensionCtrl@index');
		Route::post('/','PensionCtrl@store');
		Route::get('/{id}','PensionCtrl@show');
		Route::put('/{id}','PensionCtrl@update');
		Route::delete('/{id}','PensionCtrl@destroy');
		//Adicionales
		Route::get('/count/elem','PensionCtrl@count');
		Route::get('/search/{info}','PensionCtrl@search');
	});

	/** RUTAS INDICADORES **/
	Route::group(['prefix'=>'indicadores','middleware'=>'profesor'],function(){
		//Basicos
		Route::get('/','IndicadoresCtrl@index');
		Route::post('/','IndicadoresCtrl@store');
		Route::get('/{id}','IndicadoresCtrl@show');
		Route::put('/{id}','IndicadoresCtrl@update');
		Route::delete('/{id}','IndicadoresCtrl@destroy');
		//Adicionales
		Route::get('/count/elem','IndicadoresCtrl@count');
		Route::get('/search/{info}','IndicadoresCtrl@search');
	});

	/** RUTAS TIPONOTA **/
	Route::group(['prefix'=>'tiponota','middleware'=>'profesor'],function(){
		//Basicos
		Route::get('/','TipoNotasCtrl@index');
		Route::post('/','TipoNotasCtrl@store');
		Route::get('/{id}','TipoNotasCtrl@show');
		Route::put('/{id}','TipoNotasCtrl@update');
		Route::delete('/{id}','TipoNotasCtrl@destroy');
		//Adicionales
		Route::get('/count/elem','TipoNotasCtrl@count');
		Route::get('/search/{info}','TipoNotasCtrl@search');
	});

	/** RUTAS NOTAS **/
	Route::group(['prefix'=>'notas','middleware'=>'profesor'],function(){
		//Basicos
		Route::get('/','NotasCtrl@index');
		Route::post('/','NotasCtrl@store');
		Route::get('/{id}','NotasCtrl@show');
		Route::put('/{id}','NotasCtrl@update');
		Route::delete('/{id}','NotasCtrl@destroy');
		//Adicionales
		Route::get('/count/elem','NotasCtrl@count');
		Route::get('/search/{info}','NotasCtrl@search');
	});

	/** RUTAS AUTHDEVICE **/
	Route::group(['prefix'=>'authdevice','middleware'=>'admin'],function(){
		//Basicos
		Route::get('/','AuthdeviceCtrl@index');
		Route::post('/','AuthdeviceCtrl@store');
		Route::get('/{id}','AuthdeviceCtrl@show');
		Route::put('/{id}','AuthdeviceCtrl@update');
		Route::delete('/{id}','AuthdeviceCtrl@destroy');
		//Adicionales
		Route::get('/count/elem','AuthdeviceCtrl@count');
		Route::post('/status/{id}','AuthdeviceCtrl@modEstado');
	});

	/** RUTAS MATASISTENCIA **/
	Route::group(['prefix'=>'matasistencia','middleware'=>'coordinador'],function(){
		//Basicos
		Route::get('/','MatasistenciasCtrl@index');
		Route::post('/','MatasistenciasCtrl@store');
		Route::get('/{id}','MatasistenciasCtrl@show');
		Route::put('/{id}','MatasistenciasCtrl@update');
		Route::delete('/{id}','MatasistenciasCtrl@destroy');
		//Adicionales
		Route::get('/search/{info}','MatasistenciasCtrl@search');
		Route::get('/range/{ini}','MatasistenciasCtrl@index');
		Route::get('/count/elem','MatasistenciasCtrl@count');
		Route::get('/alumno/{id}','MatasistenciasCtrl@showalumno');
	});

	/** RUTAS NEWASISTENCIA **/
	Route::group(['prefix'=>'newasistencia','middleware'=>'coordinador'],function(){
		//Basicos
		Route::get('/','NewasistenciasCtrl@index');
		Route::post('/','NewasistenciasCtrl@store');
		Route::get('/{id}','NewasistenciasCtrl@show');
		Route::put('/{id}','NewasistenciasCtrl@update');
		Route::delete('/{id}','NewasistenciasCtrl@destroy');
		//Adicionales
		Route::get('/search/{info}','NewasistenciasCtrl@search');
		Route::get('/range/{ini}','NewasistenciasCtrl@index');
		Route::get('/count/elem','NewasistenciasCtrl@count');
		Route::get('/alumno/{id}','NewasistenciasCtrl@showalumno');
	});

	/** RUTAS ALUMNOS **/
	Route::group(['prefix'=>'alumnos','middleware'=>'coordinador'],function(){
		//Basicos
		Route::get('/','AlumnosCtrl@index');
		Route::post('/','AlumnosCtrl@store');
		Route::get('/{id}','AlumnosCtrl@show');
		Route::put('/{id}','AlumnosCtrl@update');
		Route::delete('/{id}','AlumnosCtrl@destroy');
		//Adicionales
		Route::get('/search/{info}','AlumnosCtrl@search');
	});
	
	/** RUTAS PROFESOR **/
	Route::group(['prefix'=>'profesor','middleware'=>'coordinador'],function(){
		//Basicos
		Route::get('/','ProfesorCtrl@index');
		Route::post('/','ProfesorCtrl@store');
		Route::get('/{id}','ProfesorCtrl@show');
		Route::put('/{id}','ProfesorCtrl@update');
		Route::delete('/{id}','ProfesorCtrl@destroy');
		//Adicionales
	});

	/** RUTAS MATERIAS HAS PERIODOS TABLE **/
	Route::group(['prefix'=>'materiashasperiodos','middleware'=>'coordinador'],function(){
		//Basicos
		Route::get('/','MateriasHasPeriodosCtrl@index');
		Route::post('/','MateriasHasPeriodosCtrl@store');
		Route::get('/{id}','MateriasHasPeriodosCtrl@show');
		Route::put('/{id}','MateriasHasPeriodosCtrl@update');
		Route::delete('/{id}','MateriasHasPeriodosCtrl@destroy');
		//Adicionales
	});

	/** RUTAS MATERIAS HAS NIVELES TABLE **/
	Route::group(['prefix'=>'materiashasniveles','middleware'=>'coordinador'],function(){
		//Basicos
		Route::get('/','MateriasHasNivelesCtrl@index');
		Route::post('/','MateriasHasNivelesCtrl@store');
		Route::get('/{id}','MateriasHasNivelesCtrl@show');
		Route::put('/{id}','MateriasHasNivelesCtrl@update');
		Route::delete('/{id}','MateriasHasNivelesCtrl@destroy');
		//Adicionales
	});

	/** RUTAS NIVELES HAS ANIOS TABLE **/
	Route::group(['prefix'=>'niveleshasanios','middleware'=>'coordinador'],function(){
		//Basicos
		Route::get('/','NivelesHasAniosCtrl@index');
		Route::post('/','NivelesHasAniosCtrl@store');
		Route::get('/{id}','NivelesHasAniosCtrl@show');
		Route::put('/{id}','NivelesHasAniosCtrl@update');
		Route::delete('/{id}','NivelesHasAniosCtrl@destroy');
		//Adicionales
		Route::get('add/nivelables','NivelesHasAniosCtrl@nivelables');
	});

	/** RUTAS PERIODOS TABLE **/
	Route::group(['prefix'=>'periodos','middleware'=>'coordinador'],function(){
		//Basicos
		Route::get('/','PeriodosCtrl@index');
		Route::post('/','PeriodosCtrl@store');
		Route::get('/{id}','PeriodosCtrl@show');
		Route::put('/{id}','PeriodosCtrl@update');
		Route::delete('/{id}','PeriodosCtrl@destroy');
		//Adicionales
	});

	/** RUTAS NIVELES TABLE **/
	Route::group(['prefix'=>'niveles','middleware'=>'coordinador'],function(){
		//Basicos
		Route::get('/','NivelesCtrl@index');
		Route::post('/','NivelesCtrl@store');
		Route::get('/{id}','NivelesCtrl@show');
		Route::put('/{id}','NivelesCtrl@update');
		Route::delete('/{id}','NivelesCtrl@destroy');
		//Adicionales
	});

	/** RUTAS MATERIAS TABLE **/
	Route::group(['prefix'=>'materias','middleware'=>'coordinador'],function(){
		//Basicos
		Route::get('/','MateriasCtrl@index');
		Route::post('/','MateriasCtrl@store');
		Route::get('/{id}','MateriasCtrl@show');
		Route::put('/{id}','MateriasCtrl@update');
		Route::delete('/{id}','MateriasCtrl@destroy');
		//Adicionales
	});

	/** RUTAS ANIOS TABLE **/
	Route::group(['prefix'=>'anios','middleware'=>'coordinador'],function(){
		//Basicos
		Route::get('/','AniosCtrl@index');
		Route::post('/','AniosCtrl@store');
		Route::get('/{id}','AniosCtrl@show');
		Route::put('/{id}','AniosCtrl@update');
		Route::delete('/{id}','AniosCtrl@destroy');
		//Adicionales
		Route::get('/asignado/anio','AniosCtrl@asignado');
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
		Route::get('/add/alumnable','UserCtrl@userAlumnable');
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
