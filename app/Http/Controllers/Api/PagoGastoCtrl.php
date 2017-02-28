<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use Carbon\Carbon;
use App\PagoGasto;
use Log;

class PagoGastoCtrl extends Controller
{
    /**
     * @var Request
     */
    protected $req,$rel;

    public function __construct(Request $request)//Dependency injection
    {
        $this->req = $request;
        $this->rel=[];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($ini=0)
    {
        $obj=PagoGasto::with($this->rel)
            ->orderBy('created_at','desc')
            ->skip($ini)->take(50+$ini)->get();
        $ev=new EventlogRegister;
        $msj='Consulta registros. Tabla=PagoGasto.';
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
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
        $ev->registro(1,'Intento de guardar en tabla=PagoGasto.',$this->req->user()->id);
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
        $obj=PagoGasto::with($this->rel)->findOrFail($id);
        $ev=new EventlogRegister;
        $msj='Consulta de elemento. Tabla=PagoGasto, id='.$id;
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
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
        $ev->registro(1,'Intento de modificación. Tabla=PagoGasto, id='.$id,$this->req->user()->id);
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
        $ev->registro(2,'Intento de eliminación. Tabla=PagoGasto, id='.$id,$this->req->user()->id);
        $obj=PagoGasto::findOrFail($id);
        $obj->delete();
        $msj='Borrado. Tabla=PagoGasto, id='.$id;
        $ev->registro(2,$msj,$this->req->user()->id);
        return response()->json(['msj'=>$msj]);
    }
    
    /**
     * Guarda o modifica los registros
     *
     * @return \Illuminate\Http\Response
     */
    private function setMod($id=0){
		$resultado='Operación rechazada por falta de información';
		$obj=new PagoGasto;	// Si es nuevo registro
		if($id>0){
			$obj=PagoGasto::findOrFail($id); // Si es modificacion
		}	

		//////////////////////////////////////////////////////
		// Condiciones que se repiten sea modificación o nuevo
		// Este es el lugar que se debe modificar.

        if ($id) {
            $this->validate($this->req,[
                'valor'=>'required'
            ]);
        }else{
            $this->validate($this->req,[
            	'numero_factura'=>'required',
                'valor'=>'required'
            ]);
        }
		$obj->valor=$this->req->input('valor');
		if($this->req->has('numero_factura')){
            $obj->numero_factura=$this->req->input('numero_factura');
        }
		if($this->req->has('descripcion')){
			$obj->descripcion=$this->req->input('descripcion');
		}else{
			$obj->descripcion='';
		}
		$obj->save();

		// De aqui para abajo no se toca nada
		////////////////////////////////////


		// Guardar y finalizar
		if ($id>0) {
			$resultado='Modificación. Tabla=PagoGasto, id='.$id;
		}else{
			$resultado='Elemento Creado. Tabla=PagoGasto, id='.$obj->id;
		}
		return $resultado;
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
        $obj=PagoGasto::all();
        return response()->json(['registros'=>$obj->count()]);
    }

    /**
     * Busca PagoGasto con los periodos ID. Corelaciona a los alumnos con su nota.
     *
     * @return \Illuminate\Http\Response
     */
    public function search($info){
        $obj=PagoGasto::where('numero_factura','LIKE','%'.$info.'%')
            ->orWhere('descripcion','LIKE','%'.$info.'%')
            ->orderBy('numero_factura','desc')
            ->with($this->rel)
            ->get();
        $msj='Busqueda. Tabla=PagoGasto, letras='.$info;
        $ev=new EventlogRegister;
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
    }

}
