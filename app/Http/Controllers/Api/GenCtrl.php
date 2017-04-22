<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use Carbon\Carbon;
use App\Generales;
use Log;

class GenCtrl extends Controller
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
        $obj=Generales::where('id','>',0)->skip($ini)->take(50+$ini)->get();
        $ev=new EventlogRegister;
        $msj='Consulta registros de tabla=generales.';
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
        $ev->registro(1,'Intento de guardar en tabla=generales.',$this->req->user()->id);
        $this->validate($this->req,[
            'nombre'=>'required',
            'valor'=>'required',
            'descripcion'=>'required'
        ]);
        $obj=new Generales;
        $obj->nombre=$this->req->input('nombre');
        $obj->valor=$this->req->input('valor');
        $obj->descripcion=$this->req->input('descripcion');
        $obj->save();
        $msj='Elemento Creado. Tabla=generales, id='.$obj->id;
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
        $obj=Generales::findOrFail($id);
        $ev=new EventlogRegister;
        $msj='Consulta de elemento. Tabla=generales, id='.$id;
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
        $ev->registro(1,'Intento de modificación. Tabla=generales, id='.$id,$this->req->user()->id);
        $obj=Generales::findOrFail($id);
        $this->validate($this->req,[
            'nombre'=>'required',
            'descripcion'=>'required'
        ]);
        $obj->nombre=$this->req->input('nombre');
        $obj->valor=$this->req->input('valor');
        $obj->descripcion=$this->req->input('descripcion');
        $obj->save();
        $msj='Modificación. Tabla=generales, id='.$id;
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
        $ev->registro(2,'Intento de eliminación. Tabla=generales, id='.$id,$this->req->user()->id);
        if ($id<=8) {
            return response()->json(['msj'=>'No se pueden eliminar las opciones fundamentales.']);
        }
        $obj=Generales::findOrFail($id);
        $obj->delete();
        $msj='Borrado. Tabla=generales, id='.$obj->id;
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
        $obj=Generales::all();
        return response()->json(['registros'=>$obj->count()]);
    }

    /**
     * Genera el logo por bd
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogo(){
        $obj=Generales::where('nombre','Logo')->first();
        return $obj->toJson();
    }

    /**
     * Busca los objetos que coincidan.
     *
     * @param  int  $info
     * @return \Illuminate\Http\Response
     */
    public function search($info) // Falta esto
    {
        $obj=Generales::where('nombre','LIKE','%'.$info.'%')
            ->orWhere('valor','LIKE','%'.$info.'%')
            ->orWhere('descripcion','LIKE','%'.$info.'%')
            ->get();
        $msj='Busqueda. Tabla=generales, letras='.$info;
        $ev=new EventlogRegister;
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
    }
}