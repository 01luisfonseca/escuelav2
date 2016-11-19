<?php

namespace App;

use Illuminate\Database\Eloquent\Softdeletes;
use Illuminate\Database\Eloquent\Model;

class Indicadores extends Model
{
    //
    use Softdeletes;
    protected $table = 'indicadores';

    public function tipo_nota(){
    	return $this->hasMany('App\TipoNota');
    }

    public function periodos(){
    	return $this->belongsTo('App\Periodos');
    }

    public function materias_has_niveles(){
    	return $this->belongsTo('App\MateriasHasNiveles');
    }

    public function delete(){
        $this->tipo_nota()->delete();
        parent::delete();
    }
}
