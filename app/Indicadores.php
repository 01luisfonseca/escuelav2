<?php

namespace App;

use Illuminate\Database\Eloquent\Softdeletes;
use Illuminate\Database\Eloquent\Model;

class Indicadores extends Model
{
    //
    protected $table = 'indicadores';

    public function tipo_nota(){
    	return $this->hasMany('App\TipoNota');
    }

    public function materias_has_periodos(){
    	return $this->belongsTo('App\MateriasHasPeriodos');
    }

    public function alumnos_has_indicadores(){
        return $this->hasMany('App\AlumnosHasIndicadores');
    }

}
