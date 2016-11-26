<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use App\Helpers\Borrador;
use Carbon\Carbon;
use Log;
use App\Materias;

class MateriasCtrl extends Controller
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
        $obj=Materias::with('materias_has_niveles')->skip($ini)->take(50+$ini)->orderBy('updated_at','desc')->get();
        $ev=new EventlogRegister;
        $msj='Consulta registros. Tabla=Materias.';
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
        $ev->registro(1,'Intento de guardar en tabla=Materias.',$this->req->user()->id);
        $this->validate($this->req,[
            'nombre'=>'required'
        ]);
        $val=Materias::where('nombre',$this->req->input('nombre'));
        if($val->count()){
            return response()->json(['msj'=>'El elemento ya fue creado']);
        }
        $obj=new Materias;
        $obj->nombre=$this->req->input('nombre');
        if ($this->req->has('descripcion')) {
            $obj->descripcion=$this->req->input('descripcion');
        }else{
            $obj->descripcion='';
        }
        $obj->save();
        $msj='Elemento Creado. Tabla=Materias, id='.$obj->id;
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
        $obj=Materias::with('materias_has_niveles')->findOrFail($id);
        $ev=new EventlogRegister;
        $msj='Consulta de elemento. Tabla=Materias, id='.$id;
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
        $ev->registro(1,'Intento de modificación. Tabla=Materias, id='.$id,$this->req->user()->id);
        $obj=Materias::findOrFail($id);
        $this->validate($this->req,[
            'nombre'=>'required'
        ]);
        $obj=Materias::findOrFail($id);
        $obj->nombre=$this->req->input('nombre');
        if ($this->req->has('descripcion')) {
            $obj->descripcion=$this->req->input('descripcion');
        }else{
            $obj->descripcion='';
        }
        $obj->save();
        $msj='Modificación. Tabla=Materias, id='.$id;
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
        $ev->registro(2,'Intento de eliminación. Tabla=Materias, id='.$id,$this->req->user()->id);
        $res=new Borrador;
        $res->delMaterias($id);
        $msj='Borrado. Tabla=Materias, id='.$id;
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
        $obj=Materias::all();
        return response()->json(['registros'=>$obj->count()]);
    }
}

