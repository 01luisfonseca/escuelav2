<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use App\Helpers\AlumInd;
use Carbon\Carbon;
use Log;
use App\Notas;

class NotasCtrl extends Controller
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
        $obj=Notas::with($this->rel)
            ->orderBy('updated_at','desc')
            ->skip($ini)->take(50+$ini)->get();
        $ev=new EventlogRegister;
        $msj='Consulta registros. Tabla=Notas.';
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
        $ev->registro(1,'Intento de guardar en tabla=Notas.',$this->req->user()->id);
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
        $obj=Notas::with($this->rel)->findOrFail($id);
        $ev=new EventlogRegister;
        $msj='Consulta de elemento. Tabla=Notas, id='.$id;
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
        $ev->registro(1,'Intento de modificación. Tabla=Notas, id='.$id,$this->req->user()->id);
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
        $ev->registro(2,'Intento de eliminación. Tabla=Notas, id='.$id,$this->req->user()->id);
        $res=Notas::findOrFail($id);
        $res->delete();
        $msj='Borrado. Tabla=Notas, id='.$id;
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
		$obj=new Notas;	// Si es nuevo registro
		if($id>0){
			$obj=Notas::findOrFail($id); // Si es modificacion
		}	

		//////////////////////////////////////////////////////
		// Condiciones que se repiten sea modificación o nuevo
		// Este es el lugar que se debe modificar.
        if ($id) {
            $this->validate($this->req,[
                'calificacion'=>'required'
            ]);
        }else{
            $this->validate($this->req,[
                'tipo_nota_id'=>'required',
                'alumnos_id'=>'required',
                'calificacion'=>'required'
            ]);
        }
        if($this->req->has('tipo_nota_id')){
            $obj->tipo_nota_id=$this->req->input('tipo_nota_id');
        }
        if($this->req->has('alumnos_id')){
            $obj->alumnos_id=$this->req->input('alumnos_id');
        }
		if($this->req->has('calificacion')){
			$obj->calificacion=$this->req->input('calificacion');
		}else{
			$obj->calificacion=0;
		}
		$obj->save();

        // Para actualizar el promedio de indicadores
        $nota=Notas::with('tipo_nota.indicadores')->find($obj->id);
        $alumind=new AlumInd;
        $alumind->actProm($nota->alumnos_id, $nota->tipo_nota->indicadores->id);
		
		// De aqui para abajo no se toca nada
		////////////////////////////////////


		// Guardar y finalizar
		if ($id>0) {
			$resultado='Modificación. Tabla=Notas, id='.$id;
		}else{
			$resultado='Elemento Creado. Tabla=Notas, id='.$obj->id;
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
        $obj=Notas::all();
        return response()->json(['registros'=>$obj->count()]);
    }
}
