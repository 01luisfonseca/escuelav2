<?php

namespace App;

use Illuminate\Database\Eloquent\Softdeletes;
use Illuminate\Database\Eloquent\Model;

class MateriasHasNiveles extends Model
{
    //
    use Softdeletes;
    protected $table = 'materias_has_niveles';

    public function indicadores(){
    	return $this->hasMany('App\Indicadores');
    }

    public function materias(){
    	return $this->belongsTo('App\Materias');
    }

    public function empleados(){
    	return $this->belongsTo('App\Empleados');
    }

    public function niveles_has_anios(){
    	return $this->belongsTo('App\NivelesHasAnios');
    }

    public function delete(){
        $this->indicadores()->delete();
        parent::delete();
    }
}
