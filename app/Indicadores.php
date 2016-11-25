<?php

namespace App;

use Illuminate\Database\Eloquent\Softdeletes;
use Illuminate\Database\Eloquent\Model;

class Indicadores extends Model
{
    //
    use Softdeletes;
    protected $table = 'indicadores';

    public function tipo_nota(){
    	return $this->hasMany('App\TipoNota');
    }

    public function materias_has_periodos(){
    	return $this->belongsTo('App\MateriasHasPeriodos');
    }

}
