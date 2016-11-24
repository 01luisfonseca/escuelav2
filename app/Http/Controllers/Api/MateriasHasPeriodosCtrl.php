<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use Carbon\Carbon;
use Log;
use App\MateriasHasPeriodos;

class MateriasHasPeriodosCtrl extends Controller
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
        $obj=MateriasHasPeriodos::skip($ini)->take(50+$ini)->orderBy('updated_at','desc')->get();
        $ev=new EventlogRegister;
        $msj='Consulta registros. Tabla=MateriasHasPeriodos.';
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
        $ev->registro(1,'Intento de guardar en tabla=MateriasHasPeriodos.',$this->req->user()->id);
        $this->validate($this->req,[
            'periodos_id'=>'required',
            'materias_has_niveles'=>'required'
        ]);
        $elem=MateriasHasPeriodos::where('periodos_id',$this->req->input('periodos_id'))
        	->where('materias_has_niveles',$this->req->input('materias_has_niveles'))->get();
        if($elem->count()>0){
        	return response()->json(['msj'=>'No se puede almacenar un registro con el mismo periodo y materia']);
        }
        $obj=new MateriasHasPeriodos;
        $obj->periodos_id=$this->req->input('periodos_id');
        $obj->materias_has_niveles=$this->req->input('materias_has_niveles');
        $obj->save();
        $msj='Elemento Creado. Tabla=MateriasHasPeriodos, id='.$obj->id;
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
        $obj=MateriasHasPeriodos::findOrFail($id);
        $ev=new EventlogRegister;
        $msj='Consulta de elemento. Tabla=MateriasHasPeriodos, id='.$id;
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
        $ev->registro(1,'Intento de modificación. Tabla=MateriasHasPeriodos, id='.$id,$this->req->user()->id);
        $obj=MateriasHasPeriodos::findOrFail($id);
        if ($this->req->has('periodos_id')) {
        	$obj->periodos_id=$this->req->input('periodos_id');
        }
        if ($this->req->has('materias_has_niveles')) {
        	$obj->materias_has_niveles=$this->req->input('materias_has_niveles');
        }
        $msj='Sin cambios en la tabla';
        if ($this->req->has('materias_has_niveles') || $this->req->has('periodos_id')) {
        	$obj->save();
        	$msj='Modificación. Tabla=MateriasHasPeriodos, id='.$id;
        	$ev->registro(1,$msj,$this->req->user()->id);
        }
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
        $ev->registro(2,'Intento de eliminación. Tabla=MateriasHasPeriodos, id='.$id,$this->req->user()->id);
        $obj=MateriasHasPeriodos::findOrFail($id);
        $obj->delete();
        $msj='Borrado. Tabla=MateriasHasPeriodos, id='.$obj->id;
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
        $obj=MateriasHasPeriodos::all();
        return response()->json(['registros'=>$obj->count()]);
    }
}
