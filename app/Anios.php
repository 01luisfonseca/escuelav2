<?php

namespace App;

use Illuminate\Database\Eloquent\Softdeletes;
use Illuminate\Database\Eloquent\Model;

class Anios extends Model
{
    //
    protected $table = 'anios';

    public function periodos(){
    	return $this->hasMany('App\Periodos');
    }

    public function niveles_has_anios(){
    	return $this->hasMany('App\NivelesHasAnios');
    }

    public function delete(){
    	$this->periodos()->delete();
    	$this->niveles_has_anios()->delete();
    	parent::delete();
    }

}
