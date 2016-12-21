<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use Carbon\Carbon;
use Log;
use App\Authdevice;

class AuthdeviceCtrl extends Controller
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
        $obj=Authdevice::all();
        $ev=new EventlogRegister;
        $msj='Consulta registros. Tabla=Authdevice.';
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
        $ev->registro(1,'Intento de guardar en tabla=Authdevice.',$this->req->user()->id);
        $msj=$this->setMod();
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
        $obj=Authdevice::findOrFail($id);
        $ev=new EventlogRegister;
        $msj='Consulta de elemento. Tabla=Authdevice, id='.$id;
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
        $ev->registro(1,'Intento de modificación. Tabla=Authdevice, id='.$id,$this->req->user()->id);
        $msj= $this->setMod($id);
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
        $ev->registro(2,'Intento de eliminación. Tabla=Authdevice, id='.$id,$this->req->user()->id);
        $res=Authdevice::findOrFail($id);
        $res->delete();
        $msj='Borrado. Tabla=Authdevice, id='.$id;
        $ev->registro(2,$msj,$this->req->user()->id);
        return response()->json(['msj'=>$msj]);
    }

    /////////////////////////////////////////////
    /////////// FUNCIONES ADICIONALES ///////////
    /////////////////////////////////////////////
   
    /**
     * Guarda o modifica los registros
     *
     * @return \Illuminate\Http\Response
     */
    private function setMod($id=0){
		$resultado='Operación rechazada por falta de información';
		$this->validate($this->req,[
            'nombre'=>'required',
            'serial'=>'required'
        ]);

		$obj=new Authdevice;	// Si es nuevo registro
		if($id>0){
			$obj=Authdevice::findOrFail($id); // Si es modificacion
		}

		// Condiciones que se repiten sea modificación o nuevo
		$obj->serial=$this->req->input('serial');
		if(!$this->req->input('descripcion') || is_null($this->req->input('descripcion'))){
			$obj->descripcion="";
		}else{
			$obj->descripcion=$this->req->input('descripcion');
		}
		$obj->nombre=$this->req->input('nombre');
		if($this->req->has('estado')){
			$obj->estado=$this->req->input('estado');
		}else{
			$obj->estado=true;
		}

		// Guardar y finalizar
		$obj->save();
		if ($id>0) {
			$resultado='Modificación. Tabla=Authdevice, id='.$id;
		}else{
			$resultado='Elemento Creado. Tabla=Authdevice, id='.$obj->id;
		}
		return $resultado;
	}

    /**
     * Muestra numero de registros
     *
     * @return \Illuminate\Http\Response
     */
    public function count(){
        $obj=Authdevice::all();
        return response()->json(['registros'=>$obj->count()]);
    }

    /**
     * Cambia el estado del dispositivo
     *
     * @return \Illuminate\Http\Response
     */
	public function modEstado($id){
		$ev=new EventlogRegister;
        $ev->registro(1,'Intento de modificación de estado. Tabla=Authdevice, id='.$id,$this->req->user()->id);
        $obj=Authdevice::findOrFail($id);
        $statusAnt=$obj->estado;
		$obj->estado=$this->req->input('estado');
		$obj->save();
        $msj= 'Estado modificado. De '.$this->req->estado.' a '.$statusAnt.'. Tabla=Authdevice, id='.$id;
        $ev->registro(1,$msj,$this->req->user()->id);
        return response()->json(['msj'=>$msj]);
	}
}
