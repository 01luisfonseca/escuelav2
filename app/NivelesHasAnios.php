<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Softdeletes;

class NivelesHasAnios extends Model
{
    //
    protected $table = 'niveles_has_anios';

    public function niveles(){
    	return $this->belongsTo('App\Niveles');
    }

    public function empleados(){
        return $this->belongsTo('App\Empleados');
    }

    public function materias_has_niveles(){
    	return $this->hasMany('App\MateriasHasNiveles');
    }

    public function anios(){
    	return $this->belongsTo('App\Anios');
    }
    public function alumnos(){
        return $this->hasMany('App\Alumnos');
    }
}
