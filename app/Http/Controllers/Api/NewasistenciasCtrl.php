<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use App\Helpers\Borrador;
use Carbon\Carbon;
use Log;
use App\Newasistencia;
use App\Alumnos;

class NewasistenciasCtrl extends Controller
{
/**
     * @var Request
     */
    protected $req,$relaciones;

    public function __construct(Request $request)//Dependency injection
    {
        $this->req = $request;
        $this->relaciones=[
            'alumnos.users',
            'alumnos.niveles_has_anios.niveles',
            'alumnos.niveles_has_anios.anios',
            'authdevice'
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($ini=0)
    {
        $obj=Newasistencia::with($this->relaciones)
            ->orderBy('updated_at','desc')->skip($ini)->take(50+$ini)->get();
        $ev=new EventlogRegister;
        $msj='Consulta registros. Tabla=Newasistencia.';
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
    public function store($alumnosId=0,$periodosId=0,$authdeviceId=0)
    {
        $ev=new EventlogRegister;
        $user_id=0;
        if($alumnosId==0 || $periodosId==0 || $authdeviceId==0){
        	$ev->registro(1,'Intento de guardar en tabla=Newasistencia.',$this->req->user()->id);
        	$this->validate($this->req,[
            	'alumnos_id'=>'required',
            	'periodos_id'=>'required',
                'fecha'=>'required'
        	]);
        	$user_id=$this->req->user()->id;
        }else{
        	$ev->registro(1,'Intento de guardar con dispositivo en tabla=Newasistencia.',$user_id);
        }
        $obj=new Newasistencia;
        try{
        	if ($alumnosId>0 && $periodosId>0 && $authdeviceId>0) {
        		$obj->alumnos_id=$alumnosId;
        		$obj->periodos_id=$periodosId;
        		$obj->authdevice_id=$authdeviceId;
                $obj->fecha=Carbon::now();
        	}else{
        		$obj->alumnos_id=$this->req->input('alumnos_id');
        		$obj->periodos_id=$this->req->input('periodos_id');
                $obj->fecha=new Carbon($this->req->input('fecha'));
                $obj->fecha->setTimeZone('America/Bogota');
        		$obj->authdevice_id=0; // No se guarda dispositivo porque fue generado desde el mismo PHP
        	}   
        	$obj->save();
        	$msj='Elemento Creado. Tabla=Newasistencia, id='.$obj->id;
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
        $obj=Newasistencia::with($this->relaciones)->findOrFail($id);
        $ev=new EventlogRegister;
        $msj='Consulta de elemento. Tabla=Newasistencia, id='.$id;
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
        $ev->registro(1,'Intento de modificación. Tabla=Newasistencia, id='.$id,$this->req->user()->id);
        $this->validate($this->req,[
            'alumnos_id'=>'required',
            'periodos_id'=>'required',
            'fecha'=>'required'
        ]);
        $obj=Newasistencia::findOrFail($id);
        $obj->alumnos_id=$this->req->input('alumnos_id');
        $obj->periodos_id=$this->req->input('periodos_id');
        $obj->fecha=new Carbon($this->req->input('fecha'));
        $obj->fecha->setTimeZone('America/Bogota');
        $obj->save();
        $msj='Modificación. Tabla=Newasistencia, id='.$id;
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
        $ev->registro(2,'Intento de eliminación. Tabla=Newasistencia, id='.$id,$this->req->user()->id);
        $res=new Borrador;
        $res->delNewasistencia($id);
        $msj='Borrado. Tabla=Newasistencia, id='.$id;
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
        $obj=Newasistencia::all();
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
        $obj=Newasistencia::join('alumnos','alumnos_id','=','alumnos.id')
        	->join('users','alumnos.users_id','=','users.id')
        	->join('niveles_has_anios','alumnos.niveles_has_anios_id','=','niveles_has_anios.id')
        	->join('anios','alumnos.niveles_has_anios.anios_id','=','anios.id')
            ->join('niveles','alumnos.niveles_has_anios.niveles_id','=','niveles.id')
            ->select('Newasistencia.*','users.name','users.lastname','users.identificacion')
            ->where('users.name','LIKE','%'.$info.'%')
            ->orWhere('users.lastname','LIKE','%'.$info.'%')
            ->orWhere('users.identificacion','LIKE','%'.$info.'%')
            ->orWhere('anios.anio','LIKE','%'.$info.'%')
            ->orWhere('niveles.nombre','LIKE','%'.$info.'%')
            ->orderBy('users.lastname','desc')
            ->with($this->relaciones)
            ->get();
        $msj='Busqueda. Tabla=Newasistencia, letras='.$info;
        $ev=new EventlogRegister;
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
    }

    /**
     * Muestra por alumnos ID.
     *
     * @param  int  $user
     * @return \Illuminate\Http\Response
     */
    public function showalumno($id)
    {
        $obj=Alumnos::with(['niveles_has_anios'=>function ($query) use ($id){
            $query->with(['anios'=>function($query) use ($id){
                $query->with(['periodos'=>function($query) use ($id){
                    $query->with(['newasistencia'=>function($query) use ($id){
                        $query->where('alumnos_id',$id)->orderBy('fecha','desc');
                    }]);
                }]);
            }]);
        }])->findOrFail($id);
        $ev=new EventlogRegister;
        $msj='Consulta de elemento por alumno. Tabla=Newasistencia, id='.$id;
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
    }
}
