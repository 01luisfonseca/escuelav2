<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use App\Helpers\Rellenador;
use Carbon\Carbon;
use Log;
use App\MateriasHasNiveles;

class MateriasHasNivelesCtrl extends Controller
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
        $obj=MateriasHasNiveles::skip($ini)->take(50+$ini)->orderBy('updated_at','desc')->get();
        $ev=new EventlogRegister;
        $msj='Consulta registros. Tabla=MateriasHasNiveles.';
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
        $ev->registro(1,'Intento de guardar en tabla=MateriasHasNiveles.',$this->req->user()->id);
        $this->validate($this->req,[
            'materias_id'=>'required',
            'niveles_has_anios_id'=>'required'
        ]);
        $elem=MateriasHasNiveles::where('materias_id',$this->req->input('materias_id'))
        	->where('niveles_has_anios_id',$this->req->input('niveles_has_anios_id'))->get();
        if($elem->count()>0){
        	return response()->json(['msj'=>'No se puede almacenar un registro con el mismo año y nivel']);
        }
        $obj=new MateriasHasNiveles;
        $obj->materias_id=$this->req->input('materias_id');
        $obj->niveles_has_anios_id=$this->req->input('niveles_has_anios_id');
        if ($this->req->has('empleados_id')) {
        	$obj->empleados_id=$this->req->input('empleados_id');
        }else{
        	$obj->empleados_id=0;
        }
        $obj->save();
        $msj='Elemento Creado. Tabla=MateriasHasNiveles, id='.$obj->id;
        $rell=new Rellenador;
        $msj.=' '.$rell->MateriasEnPeriodosMHP($obj->id); // Rellena las materias en nivel con los periodos existentes.
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
        $obj=MateriasHasNiveles::findOrFail($id);
        $ev=new EventlogRegister;
        $msj='Consulta de elemento. Tabla=MateriasHasNiveles, id='.$id;
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
        $ev->registro(1,'Intento de modificación. Tabla=MateriasHasNiveles, id='.$id,$this->req->user()->id);
        $obj=MateriasHasNiveles::findOrFail($id);
        if ($this->req->has('materias_id')) {
        	$obj->materias_id=$this->req->input('materias_id');
        }
        if ($this->req->has('niveles_has_anios_id')) {
        	$obj->niveles_has_anios_id=$this->req->input('niveles_has_anios_id');
        }
        if ($this->req->has('empleados_id')) {
        	$obj->empleados_id=$this->req->input('empleados_id');
        }
        $msj='Sin cambios en la tabla';
        if ($this->req->has('empleados_id') || $this->req->has('niveles_has_anios_id') || $this->req->has('materias_id')) {
        	$obj->save();
        	$msj='Modificación. Tabla=MateriasHasNiveles, id='.$id;
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
        $ev->registro(2,'Intento de eliminación. Tabla=MateriasHasNiveles, id='.$id,$this->req->user()->id);
        $obj=MateriasHasNiveles::findOrFail($id);
        $obj->delete();
        $msj='Borrado. Tabla=MateriasHasNiveles, id='.$obj->id;
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
        $obj=MateriasHasNiveles::all();
        return response()->json(['registros'=>$obj->count()]);
    }
}
