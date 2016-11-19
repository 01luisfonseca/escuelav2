<?php

namespace App;

use Illuminate\Database\Eloquent\Softdeletes;
use Illuminate\Database\Eloquent\Model;

class Materias extends Model
{
    //
    use Softdeletes;
    protected $table = 'materias';

    public function materias_has_niveles(){
    	return $this->hasMany('App\MateriasHasNiveles');
    }

    public function delete(){
        $this->materias_has_niveles()->delete();
        parent::delete();
    }
}
