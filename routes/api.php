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

	/** RUTAS PAGO GASTOS **/
	Route::group(['prefix'=>'gasto','middleware'=>'coordinador'],function(){
		//Basicos
		Route::get('/','PagoGastoCtrl@index');
		Route::post('/','PagoGastoCtrl@store');
		Route::get('/{id}','PagoGastoCtrl@show');
		Route::put('/{id}','PagoGastoCtrl@update');
		Route::delete('/{id}','PagoGastoCtrl@destroy');
		//Adicionales
		Route::get('/count/elem','PagoGastoCtrl@count');
		Route::get('/search/{info}','PagoGastoCtrl@search');
	});

	/** RUTAS LISTADOS **/
	Route::group(['prefix'=>'listados','middleware'=>'coordinador'],function(){
		Route::group(['prefix'=>'alumnos'],function(){
			//Basicos
			Route::get('/','ListadosCtrl@index'); // Obtener niveles
			Route::get('/{id}','ListadosCtrl@show'); // Obtener Alumnos
			//Adicionales
			Route::get('/exportar/{id}','ListadosCtrl@exportarAlumnos');
		});
	});

	/** RUTAS RENDIMIENTO **/
	Route::group(['prefix'=>'rendimiento','middleware'=>'alumno'],function(){
		//Basicos
		Route::get('/','RendimientoCtrl@index');
		Route::post('/','RendimientoCtrl@store');
		Route::get('/{id}','RendimientoCtrl@show');
		Route::put('/{id}','RendimientoCtrl@update');
		Route::delete('/{id}','RendimientoCtrl@destroy');
		//Adicionales
	});

	/** RUTAS PAGO OTROS **/
	Route::group(['prefix'=>'otros','middleware'=>'coordinador'],function(){
		//Basicos
		Route::get('/','OtrosCtrl@index');
		Route::post('/','OtrosCtrl@store');
		Route::get('/{id}','OtrosCtrl@show');
		Route::put('/{id}','OtrosCtrl@update');
		Route::delete('/{id}','OtrosCtrl@destroy');
		//Adicionales
		Route::get('/count/elem','OtrosCtrl@count');
		Route::get('/search/{info}','OtrosCtrl@search');
		Route::get('/fac/{fac}','OtrosCtrl@verificarFactura');
		Route::get('/alumno/{id}','OtrosCtrl@verificarAlumno');
		Route::post('/valor/porfecha','OtrosCtrl@porFecha');
	});

	/** RUTAS PAGO MATRICULA **/
	Route::group(['prefix'=>'matricula','middleware'=>'coordinador'],function(){
		//Basicos
		Route::get('/','MatriculaCtrl@index');
		Route::post('/','MatriculaCtrl@store');
		Route::get('/{id}','MatriculaCtrl@show');
		Route::put('/{id}','MatriculaCtrl@update');
		Route::delete('/{id}','MatriculaCtrl@destroy');
		//Adicionales
		Route::get('/count/elem','MatriculaCtrl@count');
		Route::get('/search/{info}','MatriculaCtrl@search');
		Route::get('/fac/{fac}','MatriculaCtrl@verificarFactura');
		Route::get('/alumno/{id}','MatriculaCtrl@verificarAlumno');
		Route::post('/valor/porfecha','MatriculaCtrl@porFecha');
	});

	/** RUTAS MESES **/
	Route::group(['prefix'=>'meses','middleware'=>'profesor'],function(){
		//Basicos
		Route::get('/','MesesCtrl@index');
		Route::post('/','MesesCtrl@store');
		Route::get('/{id}','MesesCtrl@show');
		Route::put('/{id}','MesesCtrl@update');
		Route::delete('/{id}','MesesCtrl@destroy');
		//Adicionales
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
		Route::get('/fac/{fac}','PensionCtrl@verificarFactura');
		Route::get('/alumno/{id}','PensionCtrl@verificarAlumno');
		Route::post('/valor/porfecha','PensionCtrl@porFecha');
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
	Route::group(['prefix'=>'anios'],function(){
		//Basicos
		Route::get('/','AniosCtrl@index')->middleware('coordinador');
		Route::post('/','AniosCtrl@store')->middleware('coordinador');
		Route::get('/{id}','AniosCtrl@show')->middleware('coordinador');
		Route::put('/{id}','AniosCtrl@update')->middleware('coordinador');
		Route::delete('/{id}','AniosCtrl@destroy')->middleware('coordinador');
		//Adicionales
		Route::get('/asignado/anio','AniosCtrl@asignado')->middleware('profesor');
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
	Route::group(['prefix'=>'generales'],function(){
		//Basicos
		Route::get('/','GenCtrl@index');
		Route::post('/','GenCtrl@store')->middleware('admin');
		Route::get('/{id}','GenCtrl@show')->middleware('admin');
		Route::put('/{id}','GenCtrl@update')->middleware('admin');
		Route::delete('/{id}','GenCtrl@destroy')->middleware('admin');
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
