<?php

namespace App;

use Illuminate\Database\Eloquent\Softdeletes;
use Illuminate\Database\Eloquent\Model;

class Newasistencia extends Model
{
    //
    protected $table = 'newasistencia';

    public function alumnos(){
    	return $this->belongsTo('App\Alumnos');
    }
    
    public function periodos(){
    	return $this->belongsTo('App\Periodos');
    }

    public function authdevice(){
        return $this->belongsTo('App\Authdevice');
    }
}
