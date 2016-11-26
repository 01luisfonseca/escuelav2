<?php

namespace App;

use Illuminate\Database\Eloquent\Softdeletes;
use Illuminate\Database\Eloquent\Model;

class Periodos extends Model
{
    //
    protected $table = 'periodos';

    public function materias_has_periodos(){
    	return $this->hasMany('App\MateriasHasPeriodos');
    }

    public function newasistencia(){
        return $this->hasMany('App\Newasistencia');
    }

    public function anios(){
        return $this->belongsTo('App\Anios');
    }
}
