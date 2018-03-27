<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use Carbon\Carbon;
use App\Helpers\Rellenador;
use App\Helpers\Borrador;
use Log;
use App\Meses;

class MesesCtrl extends Controller
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
        $obj=Meses::with($this->rel)
            ->orderBy('id','asc')
            ->skip($ini)->take(50+$ini)->get();
        $ev=new EventlogRegister;
        $msj='Consulta registros. Tabla=Meses.';
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
        $ev->registro(1,'Intento de guardar en tabla=Meses.',$this->req->user()->id);
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
        $obj=Meses::with($this->rel)->findOrFail($id);
        $ev=new EventlogRegister;
        $msj='Consulta de elemento. Tabla=Meses, id='.$id;
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
        $ev->registro(1,'Intento de modificación. Tabla=Meses, id='.$id,$this->req->user()->id);
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
        $ev->registro(2,'Intento de eliminación. Tabla=Meses, id='.$id,$this->req->user()->id);
        $res=new Borrador;
        $res->delMeses($id); // Usando el borrador de cascada.
        $msj='Borrado. Tabla=Meses, id='.$id;
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
		$obj=new Meses;	// Si es nuevo registro
		if($id>0){
			$obj=Meses::findOrFail($id); // Si es modificacion
		}	

		//////////////////////////////////////////////////////
		// Condiciones que se repiten sea modificación o nuevo
		// Este es el lugar que se debe modificar.

		if ($id) {
            $this->validate($this->req,[
                'nombre'=>'required'
            ]);
        }else{
            $this->validate($this->req,[
            	'nombre'=>'required'
            ]);
        }
		if($this->req->has('nombre')){
			$obj->nombre=$this->req->input('nombre');
		}else{
			$obj->nombre='';
		}
		$obj->save();

		// De aqui para abajo no se toca nada
		////////////////////////////////////


		// Guardar y finalizar
		if ($id>0) {
			$resultado='Modificación. Tabla=Meses, id='.$id;
		}else{
			$resultado='Elemento Creado. Tabla=Meses, id='.$obj->id;
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
        $obj=Meses::count();
        return response()->json(['registros'=>$obj]);
    }

    /**
     * Busca Meses con los periodos ID. Corelaciona a los alumnos con su nota.
     *
     * @return \Illuminate\Http\Response
     */
    public function search($info){
        $obj=Meses::join('alumnos','alumnos_id','=','alumnos.id')
            ->join('users','alumnos.users_id','=','users.id')
            ->select('pago_pension.*')
            ->where('alumnos.users.name','LIKE','%'.$info.'%')
            ->orWhere('alumnos.users.lastname','LIKE','%'.$info.'%')
            ->orWhere('alumnos.users.identificacion','LIKE','%'.$info.'%')
            ->orWhere('numero_factura','LIKE','%'.$info.'%')
            ->orWhere('niveles.nombre','LIKE','%'.$info.'%')
            ->orderBy('users.lastname','desc')
            ->with($this->rel)
            ->get();
        $msj='Busqueda. Tabla=Meses, letras='.$info;
        $ev=new EventlogRegister;
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
    }
}
