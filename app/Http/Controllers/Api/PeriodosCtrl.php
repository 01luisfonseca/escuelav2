<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use Carbon\Carbon;
use Log;
use App\Periodos;

class PeriodosCtrl extends Controller
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
        $obj=Periodos::with('anios')->skip($ini)->take(50+$ini)->orderBy('updated_at','desc')->get();
        $ev=new EventlogRegister;
        $msj='Consulta registros. Tabla=Periodos.';
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
        $ev->registro(1,'Intento de guardar en tabla=Periodos.',$this->req->user()->id);
        $this->validate($this->req,[
            'nombre'=>'required',
            'anios_id'=>'required'
        ]);
        $obj=new Periodos;
        $obj->nombre=$this->req->input('nombre');
        $obj->anios_id=$this->req->input('anios_id');
        if ($this->req->has('descripcion')) {
            $obj->descripcion=$this->req->input('descripcion');
        }else{
            $obj->descripcion='';
        }
        $obj->save();
        $msj='Elemento Creado. Tabla=Periodos, id='.$obj->id;
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
        $obj=Periodos::with('anios')->findOrFail($id);
        $ev=new EventlogRegister;
        $msj='Consulta de elemento. Tabla=Periodos, id='.$id;
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
        $ev->registro(1,'Intento de modificación. Tabla=Periodos, id='.$id,$this->req->user()->id);
        $obj=Periodos::findOrFail($id);
        $this->validate($this->req,[
            'nombre'=>'required',
            'anios_id'=>'required'
        ]);
        $obj=Periodos::findOrFail($id);
        $obj->nombre=$this->req->input('nombre');
        $obj->anios_id=$this->req->input('anios_id');
        if ($this->req->has('descripcion')) {
            $obj->descripcion=$this->req->input('descripcion');
        }else{
            $obj->descripcion='';
        }
        $obj->save();
        $msj='Modificación. Tabla=Periodos, id='.$id;
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
        $ev->registro(2,'Intento de eliminación. Tabla=Periodos, id='.$id,$this->req->user()->id);
        $obj=Periodos::findOrFail($id);
        $obj->delete();
        $msj='Borrado. Tabla=Periodos, id='.$obj->id;
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
        $obj=Periodos::all();
        return response()->json(['registros'=>$obj->count()]);
    }
}
