<?php

namespace App;

use Illuminate\Database\Eloquent\Softdeletes;
use Illuminate\Database\Eloquent\Model;

class Matasistencia extends Model
{
    //
    protected $table = 'matasistencia';

    public function alumnos(){
    	return $this->belongsTo('App\Alumnos');
    }
    
    public function materias_has_periodos(){
    	return $this->belongsTo('App\MateriasHasPeriodos');
    }

    public function authdevice(){
        return $this->belongsTo('App\Authdevice');
    }
}