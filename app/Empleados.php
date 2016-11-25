<?php

namespace App;

use Illuminate\Database\Eloquent\Softdeletes;
use Illuminate\Database\Eloquent\Model;

class Empleados extends Model
{
    //
    use Softdeletes;
    protected $table = 'empleados';

    public function niveles_has_anios(){
    	return $this->hasMany('App\NivelesHasAnios');
    }

    public function materias_has_niveles(){
        return $this->hasMany('App\MateriasHasNiveles');
    }

    public function pago_salario(){
    	return $this->hasMany('App\PagoSalario');
    }

    public function users(){
    	return $this->belongsTo('App\User');
    }

}
