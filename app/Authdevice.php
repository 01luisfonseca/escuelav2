<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Softdeletes;

class Authdevice extends Model
{
    //
    protected $table = 'authdevice';
    use Softdeletes;

    public function newasistencia(){
    	return $this->hasMany('App\Newasistencia');
    }

    public function matasistencia(){
    	return $this->hasMany('App\Matasistencia');
    }

}
