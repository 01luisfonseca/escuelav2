<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Softdeletes;

class NivelesHasAnios extends Model
{
    //
    protected $table = 'niveles_has_anios';

    use Softdeletes;

    public function niveles(){
    	return $this->belongsTo('App\Niveles');
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

    public function delete(){
        $this->materias_has_niveles()->delete();
        $this->alumnos()->delete();
        parent::delete();
    }
}
