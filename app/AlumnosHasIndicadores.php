<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlumnosHasIndicadores extends Model
{
    //
    protected $table = 'alumnos_has_indicadores';

    public function indicadores(){
    	return $this->belongsTo('App\Indicadores');
    }

    public function alumnos(){
    	return $this->belongsTo('App\Alumnos');
    }

}