<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use App\Helpers\Borrador;
use Carbon\Carbon;
use Log;
use App\Matasistencia;
use App\Alumnos;

class MatasistenciasCtrl extends Controller
{
/**
     * @var Request
     */
    protected $req, $relaciones;

    public function __construct(Request $request)//Dependency injection
    {
        $this->req = $request;
        $this->relaciones=[
            'alumnos.users',
            'materias_has_periodos.periodos.anios',
            'authdevice',
            'materias_has_periodos.materias_has_niveles.materias',
            'materias_has_periodos.materias_has_niveles.niveles_has_anios.niveles'
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($ini=0)
    {
        $obj=Matasistencia::with($this->relaciones)
            ->orderBy('updated_at','desc')->skip($ini)->take(50+$ini)->get();
        $ev=new EventlogRegister;
        $msj='Consulta registros. Tabla=Matasistencia.';
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
    public function store($alumnosId=0,$materiashasperiodosId=0,$authdeviceId=0)
    {
        $ev=new EventlogRegister;
        $user_id=0;
        if($alumnosId==0 || $materiasHasPeriodosId==0 || $authdeviceId==0){
        	$ev->registro(1,'Intento de guardar en tabla=Matasistencia.',$this->req->user()->id);
        	$this->validate($this->req,[
            	'alumnos_id'=>'required',
            	'materias_has_periodos_id'=>'required',
                'fecha'=>'required'
        	]);
        	$user_id=$this->req->user()->id;
        }else{
        	$ev->registro(1,'Intento de guardar con dispositivo en tabla=Matasistencia.',$user_id);
        }
        $obj=new Matasistencia;
        try{
        	if ($alumnosId>0 && $periodosId>0 && $authdeviceId>0) {
        		$obj->alumnos_id=$alumnosId;
        		$obj->materias_has_periodos_id=$materiashasperiodosId;
        		$obj->authdevice_id=$authdeviceId;
                $obj->fecha=Carbon::now();
        	}else{
        		$obj->alumnos_id=$this->req->input('alumnos_id');
        		$obj->materias_has_periodos_id=$this->req->input('materias_has_periodos_id');
                $obj->fecha=new Carbon($this->req->input('fecha'));
                $obj->fecha->setTimeZone('America/Bogota');
        		$obj->authdevice_id=0; // No se guarda dispositivo porque fue generado desde el mismo PHP
        	}   
        	$obj->save();
        	$msj='Elemento Creado. Tabla=Matasistencia, id='.$obj->id;
        	$ev->registro(1,$msj,$user_id);
        	return response()->json(['msj'=>$msj]);
    	}
    	catch(Exception $e){
    		return response()->json(['msj'=>'No se ha grabado registro']);
    	}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $obj=Matasistencia::with($this->relaciones)->findOrFail($id);
        $ev=new EventlogRegister;
        $msj='Consulta de elemento. Tabla=Matasistencia, id='.$id;
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
        $ev->registro(1,'Intento de modificación. Tabla=Matasistencia, id='.$id,$this->req->user()->id);
        $this->validate($this->req,[
            'alumnos_id'=>'required',
            'materias_has_periodos_id'=>'required',
            'fecha'=>'required'
        ]);
        $obj=Matasistencia::findOrFail($id);
        $obj->alumnos_id=$this->req->input('alumnos_id');
        $obj->materias_has_periodos_id=$this->req->input('materias_has_periodos_id');
        $obj->fecha=new Carbon($this->req->input('fecha'));
        $obj->fecha->setTimeZone('America/Bogota');
        $obj->save();
        $msj='Modificación. Tabla=Matasistencia, id='.$id;
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
        $ev->registro(2,'Intento de eliminación. Tabla=Matasistencia, id='.$id,$this->req->user()->id);
        $res=new Borrador;
        $res->delMatasistencia($id);
        $msj='Borrado. Tabla=Matasistencia, id='.$id;
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
        $obj=Matasistencia::all();
        return response()->json(['registros'=>$obj->count()]);
    }

    /**
     * Busca los objetos que coincidan.
     *
     * @param  int  $info
     * @return \Illuminate\Http\Response
     */
    public function search($info)
    {
        $obj=Matasistencia::join('alumnos','alumnos_id','=','alumnos.id')
        	->join('users','alumnos.users_id','=','users.id')
        	->join('niveles_has_anios','alumnos.niveles_has_anios_id','=','niveles_has_anios.id')
        	->join('anios','alumnos.niveles_has_anios.anios_id','=','anios.id')
            ->join('niveles','alumnos.niveles_has_anios.niveles_id','=','niveles.id')
            ->join('materias','materias_has_periodos.materias_has_niveles.niveles')
            ->select('Matasistencia.*')
            ->where('users.name','LIKE','%'.$info.'%')
            ->orWhere('users.lastname','LIKE','%'.$info.'%')
            ->orWhere('users.identificacion','LIKE','%'.$info.'%')
            ->orWhere('anios.anio','LIKE','%'.$info.'%')
            ->orWhere('niveles.nombre','LIKE','%'.$info.'%')
            ->orWhere('materias.nombre','LIKE','%'.$info.'%')
            ->orderBy('users.lastname','desc')
            ->with($this->relaciones)
            ->get();
        $msj='Busqueda. Tabla=Matasistencia, letras='.$info;
        $ev=new EventlogRegister;
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
    }

    public function showalumno($id)
    {
        $obj=Alumnos::with(['niveles_has_anios'=>function ($query) use ($id){
            $query->with(['materias_has_niveles'=>function($query) use ($id){
                $query->with(['materias_has_periodos'=>function($query) use ($id){
                    $query->with(['materias', 'matasistencia'=>function($query) use ($id){
                        $query->where('alumnos_id',$id)->orderBy('fecha','desc');
                    }]);
                }]);
            }]);
        }])->findOrFail($id);
        $ev=new EventlogRegister;
        $msj='Consulta de elemento por alumno. Tabla=Matasistencia, id='.$id;
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
    }
}