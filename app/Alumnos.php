<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Softdeletes;

class Alumnos extends Model
{
    //
    use Softdeletes;

    protected $table = 'alumnos';

    public function pago_pension(){
    	return $this->hasMany('App\PagoPension');
    }

    public function pago_matricula(){
    	return $this->hasMany('App\PagoMatricula');
    }

    public function pago_otro(){
        return $this->hasMany('App\PagoOtros');
    }

    public function users(){
    	return $this->belongsTo('App\User');
    }

    public function niveles_has_anios(){
    	return $this->belongsTo('App\NivelesHasAnios');
    }

    public function notas(){
        return $this->hasMany('App\Notas');
    }

    public function newasistencia(){
        return $this->hasMany('App\Newasistencia');
    }

    public function matasistencia(){
        return $this->hasMany('App\Matasistencia');
    }

    public function alumnos_has_indicadores(){
        return $this->hasMany('App\AlumnosHasIndicadores');
    }
}
