<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use Carbon\Carbon;
use App\Helpers\Rellenador;
use App\Helpers\Borrador;
use Log;
use App\Indicadores;
use App\MateriasHasPeriodos;

class IndicadoresCtrl extends Controller
{
    /**
     * @var Request
     */
    protected $req,$rel;

    public function __construct(Request $request)//Dependency injection
    {
        $this->req = $request;
        $this->rel=['tipo_nota.notas'];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($ini=0)
    {
        $obj=Indicadores::with($this->rel)
            ->orderBy('updated_at','desc')
            ->skip($ini)->take(50+$ini)->get();
        $ev=new EventlogRegister;
        $msj='Consulta registros. Tabla=Indicadores.';
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
        $ev->registro(1,'Intento de guardar en tabla=Indicadores.',$this->req->user()->id);
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
        $obj=Indicadores::with($this->rel)->findOrFail($id);
        $ev=new EventlogRegister;
        $msj='Consulta de elemento. Tabla=Indicadores, id='.$id;
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
        $ev->registro(1,'Intento de modificación. Tabla=Indicadores, id='.$id,$this->req->user()->id);
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
        $ev->registro(2,'Intento de eliminación. Tabla=Indicadores, id='.$id,$this->req->user()->id);
        $res=new Borrador;
        $res->delIndicadores($id); // Usando el borrador de cascada.
        $msj='Borrado. Tabla=Indicadores, id='.$id;
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
		$obj=new Indicadores;	// Si es nuevo registro
		if($id>0){
			$obj=Indicadores::findOrFail($id); // Si es modificacion
		}	

		//////////////////////////////////////////////////////
		// Condiciones que se repiten sea modificación o nuevo
		// Este es el lugar que se debe modificar.

		$this->validate($this->req,[
            'nombre'=>'required',
            'porcentaje'=>'required',
            'materias_has_periodos_id'=>'required'
        ]);
		$obj->nombre=$this->req->input('nombre');
		$obj->porcentaje=$this->req->input('porcentaje');
		$obj->materias_has_periodos_id=$this->req->input('materias_has_periodos_id');
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
			$resultado='Modificación. Tabla=Indicadores, id='.$id;
		}else{
			$resultado='Elemento Creado. Tabla=Indicadores, id='.$obj->id;
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
        $obj=Indicadores::all();
        return response()->json(['registros'=>$obj->count()]);
    }

    /**
     * Busca indicadores con los periodos ID. Corelaciona a los alumnos con su nota.
     *
     * @return \Illuminate\Http\Response
     */
    public function search($info){
        $indicadoresRaw=Indicadores::with(['tipo_nota'=>function($query1){
                $query1->orderBy('id','asc')->with('notas');
            }])->where('materias_has_periodos_id',$info)->get();
        $periodo=MateriasHasPeriodos::with('materias_has_niveles.niveles_has_anios.alumnos.users')->where('id',$info)->first(); // Hacia usuarios, para sacar a los alumnos
        $alumnos=array();
        foreach ($periodo->materias_has_niveles->niveles_has_anios->alumnos as $alumno) {
            // Rellenamos la tabla del alumno y las notas
            $alumnos[]=[
                'id'=>$alumno->id,
                'users_id'=>$alumno->users_id,
                'name'=>$alumno->users->name,
                'lastname'=>$alumno->users->lastname,
                'tipo_nota'=>[]
            ]; // Acá vamos. Falta crear tabla de indicadores y dentro los alumnos, y demtro de alumnos el tipo de notas.
        }
        // Para ordenar los registros de alumno. Es quien da el orden de los nombres.
        $temp=collect($alumnos);
        $sorted=$temp->sortBy('lastname');
        $alumnos=$sorted->all();
        $indicadores=array();
        // Truco para deshacerme de las llaves personalizadas
        $tempo=[];
        foreach ($alumnos as $key => $value) {
            $tempo[]=$value;
        }
        // Creo la tabla principal de indicadores
        foreach ($indicadoresRaw as $indicador) {
            // Asignamos información básica del indicador y alumnos
            $indicadores[]=[
                'id'=>$indicador->id,
                'nombre'=>$indicador->nombre,
                'descripcion'=>$indicador->descripcion,
                'porcentaje'=>$indicador->porcentaje,
                'tipo_nota'=>array(),
                'alumnos'=>$tempo,
                ];
        }
        $i=0;
        // Se recargan los tipos de notas y los alumnos con sus notas
        foreach ($indicadoresRaw as $indicador) {
            foreach ($indicador->tipo_nota as $tipo) {
                // Revisamos cada nota del indicador
                foreach ($tipo->notas as $nota) {
                    // Revisamos cada alumno
                    foreach ($indicadores[$i]['alumnos'] as &$alumno) {
                        // Verificamos que cada nota tenga un alumno. Al encontrarlo ingresamos la nota en los tipos de nota propios del alumno.
                        if($nota->alumnos_id==$alumno['id']){
                            array_push($alumno['tipo_nota'], ['id'=>$tipo->id,'notas_id'=>$nota->id,'cal'=>$nota->calificacion]);
                        }
                        unset($alumno); // Requerido para el foreach con apuntamiento
                    }
                }
                // Agregamos los tipos de nota del indicador
                array_push($indicadores[$i]['tipo_nota'], ['id'=>$tipo->id,'nombre'=>$tipo->nombre,'descripcion'=>$tipo->descripcion]);
            }
            $i++;
        }
        $indicadoresObj=collect($indicadores);
        return $indicadoresObj->toJson();
    }
}
