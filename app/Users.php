<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Users extends Authenticatable
{

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    protected $dates = ['deleted_at'];

    public function tipo_usuario(){
        return $this->belongsTo('App\TipoUsuario');
    }

    public function eventlog(){
        return $this->hasMany('App\Eventlog');
    }

    public function alumnos(){
        return $this->hasMany('App\Alumnos');
    }

    public function empleados(){
        return $this->hasMany('App\Empleados');
    }
}
