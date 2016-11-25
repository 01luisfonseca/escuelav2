<?php

namespace App;

use Illuminate\Database\Eloquent\Softdeletes;
use Illuminate\Database\Eloquent\Model;

class MateriasHasPeriodos extends Model
{
    //
    protected $table = 'materias_has_periodos';

    public function materias_has_niveles(){
    	return $this->belongsTo('App\MateriasHasNiveles');
    }

    public function periodos(){
    	return $this->belongsTo('App\Periodos');
    }

    public function indicadores(){
    	return $this->hasMany('App\Indicadores');
    }

    public function matasistencia(){
        return $this->hasMany('App\Matasistencia');
    }
}
