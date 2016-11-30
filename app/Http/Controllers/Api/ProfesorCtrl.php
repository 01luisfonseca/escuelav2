<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use App\Helpers\Borrador;
use Carbon\Carbon;
use App\MateriasHasNiveles;
use App\Empleados;
use Log;

class ProfesorCtrl extends Controller
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
        $obj=Empleados::join('users','users_id','=','users.id')
            ->select('empleados.*','users.tipo_usuario_id')
            ->where('users.tipo_usuario_id','>',3)
            ->with('users.tipo_usuario')->skip($ini)->take(50+$ini)->orderBy('updated_at','desc')->get();
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $ev->registro(1,'Intento de guardar profesor en tabla=MateriasHasNiveles.',$this->req->user()->id);
        $this->validate($this->req,[
            'empleados_id'=>'required'
        ]);
        $obj=MateriasHasNiveles::findOrFail($id);
        $obj->empleados_id=$this->req->input('empleados_id');
        $obj->save();
        $msj='Profesor modificado. Tabla=MateriasHasNiveles, id='.$obj->id;
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
        $ev->registro(2,'Intento de eliminación de profesor. Tabla=MateriasHasNiveles, id='.$id,$this->req->user()->id);
        $obj=MateriasHasNiveles::findOrFail($id);
        $obj->empleados_id=0;
        $obj->save();
        $msj='Profesor Borrado. Tabla=MateriasHasNiveles, id='.$id;
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
