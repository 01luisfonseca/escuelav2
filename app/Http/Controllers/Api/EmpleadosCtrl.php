<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use App\Helpers\Borrador;
use Carbon\Carbon;
use App\Empleados;
use Log;

class EmpleadosCtrl extends Controller
{
    /**
     * @var Request
     */
    protected $req;

    public function __construct(Request $request)//Dependency injection
    {
        $this->req = $request;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($ini=0)
    {
        $obj=Empleados::with('users.tipo_usuario')->skip($ini)->take(10)->orderBy('updated_at','desc')->get();
        $ev=new EventlogRegister;
        $msj='Consulta registros. Tabla=Empleados.';
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $ev=new EventlogRegister;
        $ev->registro(1,'Intento de guardar en tabla=Empleados.',$this->req->user()->id);
        $this->validate($this->req,[
            'users_id'=>'required',
            'salario'=>'required',
            'eps'=>'required',
            'eps_val'=>'required',
            'arl'=>'required',
            'arl_val'=>'required',
            'pension'=>'required',
            'pension_val'=>'required',
            'contrato_ini'=>'required',
            'contrato_fin'=>'required'
        ]);
        $date1= new Carbon($this->req->input('contrato_ini'));
        $date2= new Carbon($this->req->input('contrato_fin'));
        $obj=new Empleados;
        $obj->users_id=$this->req->input('users_id');
        $obj->salario=$this->req->input('salario');
        $obj->salario_pagado=0;
        $obj->eps=$this->req->input('eps');
        $obj->eps_val=$this->req->input('eps_val');
        $obj->arl=$this->req->input('arl');
        $obj->arl_val=$this->req->input('arl_val');
        $obj->pension=$this->req->input('pension');
        $obj->pension_val=$this->req->input('pension_val');
        $obj->contrato_ini=$date1;
        $obj->contrato_fin=$date2;
        $obj->save();
        $msj='Elemento Creado. Tabla=Empleados, id='.$obj->id;
        $ev->registro(1,$msj,$this->req->user()->id);
        return response()->json(['msj'=>$msj]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $obj=Empleados::with('users')->findOrFail($id);
        $ev=new EventlogRegister;
        $msj='Consulta de elemento. Tabla=Empleados, id='.$id;
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $user
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $ev=new EventlogRegister;
        $ev->registro(1,'Intento de modificación. Tabla=Empleados, id='.$id,$this->req->user()->id);
        $obj=Empleados::findOrFail($id);
        $this->validate($this->req,[
            'users_id'=>'required',
            'salario'=>'required',
            'eps'=>'required',
            'eps_val'=>'required',
            'arl'=>'required',
            'arl_val'=>'required',
            'pension'=>'required',
            'pension_val'=>'required',
            'contrato_ini'=>'required',
            'contrato_fin'=>'required'
        ]);
        $date1= new Carbon($this->req->input('contrato_ini'));
        $date2= new Carbon($this->req->input('contrato_fin'));
        $obj->users_id=$this->req->input('users_id');
        $obj->salario=$this->req->input('salario');
        $obj->salario_pagado=0;
        $obj->eps=$this->req->input('eps');
        $obj->eps_val=$this->req->input('eps_val');
        $obj->arl=$this->req->input('arl');
        $obj->arl_val=$this->req->input('arl_val');
        $obj->pension=$this->req->input('pension');
        $obj->pension_val=$this->req->input('pension_val');
        $obj->contrato_ini=$date1;
        $obj->contrato_fin=$date2;
        $obj->save();
        $msj='Modificación. Tabla=Empleados, id='.$id;
        $ev->registro(1,$msj,$this->req->user()->id);
        return response()->json(['msj'=>$msj]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ev=new EventlogRegister;
        $ev->registro(2,'Intento de eliminación. Tabla=Empleados, id='.$id,$this->req->user()->id);
        $res=new Borrador;
        $res->delEmpleados($id);
        $msj='Borrado. Tabla=Empleados, id='.$id;
        $ev->registro(2,$msj,$this->req->user()->id);
        return response()->json(['msj'=>$msj]);
    }

    /////////////////////////////////////////////
    /////////// FUNCIONES ADICIONALES ///////////
    /////////////////////////////////////////////
   
    /**
     * Muestra numero de registros
     *
     * @return \Illuminate\Http\Response
     */
    public function count(){
        $obj=Empleados::all();
        return response()->json(['registros'=>$obj->count()]);
    }

    /**
     * Busca los objetos que coincidan.
     *
     * @param  int  $info
     * @return \Illuminate\Http\Response
     */
    public function search($info) // Falta esto
    {
        $obj=Empleados::join('users','users_id','=','users.id')
            ->select('empleados.*','users.name','users.lastname','users.identificacion')
            ->where('eps','LIKE','%'.$info.'%')
            ->orWhere('arl','LIKE','%'.$info.'%')
            ->orWhere('pension','LIKE','%'.$info.'%')
            ->orWhere('users.name','LIKE','%'.$info.'%')
            ->orWhere('users.lastname','LIKE','%'.$info.'%')
            ->orWhere('identificacion','LIKE','%'.$info.'%')
            ->orderBy('users.lastname','desc')
            ->with('users')
            ->get();
        $msj='Busqueda. Tabla=Empleados, letras='.$info;
        $ev=new EventlogRegister;
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
    }
}
