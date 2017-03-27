<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/',function(){
    return view('index');
});

Route::get('/logo','Api\GenCtrl@getLogo');

/*Route::group(['middleware'=>'checkSerial', 'namespace'=>'registro'],function(){
	Route::group(['prefix'=>'{serial}/device'],function(){
		Route::get('/status',function(){
			return response('Habilitado',200);
		});
		Route::post('/asistencia/{tarjeta}','AsistenciaCtrl@postAsistencia');
		Route::get('/asistencia/{tarjeta}','AsistenciaCtrl@getDeviceAsistencia');
		Route::get('/asistencia','AsistenciaCtrl@getOnlyTarjetas'); // Probadas para la carga
		Route::get('/all','AuthdeviceCtrl@getDevices');
	});
});*/