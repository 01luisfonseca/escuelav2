<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlumnosHasPeriodos extends Model
{
    //
    protected $table = 'alumnos_has_periodos';

    public function materias_has_periodos(){
    	return $this->belongsTo('App\MateriasHasPeriodos');
    }

    public function alumnos(){
    	return $this->belongsTo('App\Alumnos');
    }

}