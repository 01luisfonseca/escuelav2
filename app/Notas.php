<?php

namespace App;

use Illuminate\Database\Eloquent\Softdeletes;
use Illuminate\Database\Eloquent\Model;

class Notas extends Model
{
    //
    protected $table = 'notas';

    public function alumnos(){
    	return $this->belongsTo('App\Alumnos');
    }

    public function tipo_nota(){
    	return $this->belongsTo('App\TipoNota');
    }
}
