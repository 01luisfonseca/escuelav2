<?php

namespace App;

use Illuminate\Database\Eloquent\Softdeletes;
use Illuminate\Database\Eloquent\Model;

class TipoNota extends Model
{
    //
    use Softdeletes;
    protected $table = 'tipo_nota';

    public function notas(){
    	return $this->hasMany('App\Notas');
    }

    public function indicadores(){
    	return $this->belongsTo('App\Indicadores');
    }

    public function delete(){
        $this->notas()->delete();
        parent::delete();
    }
}
