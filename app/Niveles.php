<?php

namespace App;

use Illuminate\Database\Eloquent\Softdeletes;
use Illuminate\Database\Eloquent\Model;

class Niveles extends Model
{
    //
    protected $table = 'niveles';

    public function niveles_has_anios(){
    	return $this->hasMany('App\NivelesHasAnios');
    }

    public function delete(){
        $this->niveles_has_anios()->delete();
        parent::delete();
    }

}
