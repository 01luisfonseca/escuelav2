<?php

namespace App;

use Illuminate\Database\Eloquent\Softdeletes;
use Illuminate\Database\Eloquent\Model;

class Periodos extends Model
{
    //
    use Softdeletes;
    protected $table = 'periodos';

    public function indicadores(){
    	return $this->hasMany('App\Indicadores');
    }

    public function newasistencia(){
        return $this->hasMany('App\Newasistencia');
    }

    public function anios(){
        return $this->belongsTo('App\Anios');
    }

    public function delete(){
        $this->indicadores()->delete();
        $this->newasistencia()->delete();
        parent::delete();
    }
}
