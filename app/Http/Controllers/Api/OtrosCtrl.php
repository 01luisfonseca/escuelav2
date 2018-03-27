<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use Carbon\Carbon;
use App\Helpers\Verificador;
use App\Helpers\Rellenador;
use App\Helpers\Borrador;
use Log;
use App\PagoOtros;

class OtrosCtrl extends Controller
{
    /**
     * @var Request
     */
    protected $req,$rel;

    public function __construct(Request $request)//Dependency injection
    {
        $this->req = $request;
        $this->rel=['alumnos.users','alumnos.niveles_has_anios.anios','alumnos.niveles_has_anios.niveles'];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($ini=0)
    {
        $obj=PagoOtros::with($this->rel)
            ->orderBy('created_at','desc')
            ->skip($ini)->take(50+$ini)->get();
        $ev=new EventlogRegister;
        $msj='Consulta registros. Tabla=PagoOtros.';
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
        $ev->registro(1,'Intento de guardar en tabla=PagoOtros.',$this->req->user()->id);
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
        $obj=PagoOtros::with($this->rel)->findOrFail($id);
        $ev=new EventlogRegister;
        $msj='Consulta de elemento. Tabla=PagoOtros, id='.$id;
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
        $ev->registro(1,'Intento de modificación. Tabla=PagoOtros, id='.$id,$this->req->user()->id);
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
        $ev->registro(2,'Intento de eliminación. Tabla=PagoOtros, id='.$id,$this->req->user()->id);
        $res=new Borrador;
        $res->delPagoOtros($id); // Usando el borrador de cascada.
        $msj='Borrado. Tabla=PagoOtros, id='.$id;
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
		$obj=new PagoOtros;	// Si es nuevo registro
		if($id>0){
			$obj=PagoOtros::findOrFail($id); // Si es modificacion
		}	

		//////////////////////////////////////////////////////
		// Condiciones que se repiten sea modificación o nuevo
		// Este es el lugar que se debe modificar.

        if ($id) {
            $this->validate($this->req,[
                'cancelado_at'=>'required',
                'valor'=>'required'
            ]);
        }else{
            $this->validate($this->req,[
            	'numero_factura'=>'required',
            	'alumnos_id'=>'required',
                'valor'=>'required'
            ]);
            $ver=new Verificador;
            if($ver->existeFactura($this->req->input('numero_factura'))){
                return 'Factura ya existe. No se guarda nada';
            }

        }
		$obj->valor=$this->req->input('valor');
		if($this->req->has('numero_factura')){
            $obj->numero_factura=$this->req->input('numero_factura');
        }
        if($this->req->has('alumnos_id')){
            $obj->alumnos_id=$this->req->input('alumnos_id');
        }
        if($this->req->has('cancelado_at')){
            $obj->cancelado_at=new Carbon($this->req->input('cancelado_at'));
        }else{
            $obj->cancelado_at=new Carbon();
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
			$resultado='Modificación. Tabla=PagoOtros, id='.$id;
		}else{
			$resultado='Elemento Creado. Tabla=PagoOtros, id='.$obj->id;
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
        $obj=PagoOtros::all();
        Log::info($obj);
        return response()->json(['registros'=>$obj->count()]);
    }

    /**
     * Busca PagoOtros con los periodos ID. Corelaciona a los alumnos con su nota.
     *
     * @return \Illuminate\Http\Response
     */
    public function search($info){
        $obj=PagoOtros::join('alumnos','alumnos_id','=','alumnos.id')
            ->join('users','alumnos.users_id','=','users.id')
            ->select('pago_otro.*')
            ->where('users.name','LIKE','%'.$info.'%')
            ->orWhere('users.lastname','LIKE','%'.$info.'%')
            ->orWhere('users.identificacion','LIKE','%'.$info.'%')
            ->orWhere('numero_factura','LIKE','%'.$info.'%')
            ->orderBy('users.lastname','desc')
            ->with($this->rel)
            ->get();
        $msj='Busqueda. Tabla=PagoOtros, letras='.$info;
        $ev=new EventlogRegister;
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
    }

    /**
     * Busca registros con el mismo numero de factura
     *
     * @return \Illuminate\Http\Response
     */
    public function verificarFactura($fac=0)
    {
        $obj=PagoOtros::with($this->rel)->where('numero_factura',$fac)->firstOrFail();
        return $obj->toJson();
    }

    /**
     * Muestra resultados según alumno.
     *
     * @param  int  $user
     * @return \Illuminate\Http\Response
     */
    public function verificarAlumno($id)
    {
        $obj=PagoOtros::with($this->rel)->where('alumnos_id',$id)->get();
        $ev=new EventlogRegister;
        $msj='Consulta de elementos según alumnos. Tabla=PagoOtros, alumnos_id='.$id;
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
    }

    /**
     * Muestra resultados según fecha.
     *
     * @param  $request
     * @return \Illuminate\Http\Response
     */
    public function porFecha()
    {
        $fecha=substr($this->req->input('fecha'), 0, 10);
        $objeto=PagoOtros::with(['alumnos.users'])
            ->where('created_at','LIKE','%'.$fecha.'%')
            ->orderBy('numero_factura','asc')
            ->get();
        return $objeto->toJson();
    }
    
}
