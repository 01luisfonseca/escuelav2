<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use App\Helpers\Borrador;
use App\Helpers\Rellenador;
use Carbon\Carbon;
use Log;
use App\Alumnos;

class AlumnosCtrl extends Controller
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
        $obj=Alumnos::with('users.tipo_usuario','niveles_has_anios.anios','niveles_has_anios.niveles')
            ->orderBy('updated_at','desc')->skip($ini)->take(20)->get();
        $ev=new EventlogRegister;
        $msj='Consulta registros. Tabla=Alumnos.';
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
        $ev->registro(1,'Intento de guardar en tabla=Alumnos.',$this->req->user()->id);
        $this->validate($this->req,[
            'users_id'=>'required',
            'acudiente'=>'required',
            'niveles_has_anios_id'=>'required',
            'pension'=>'required',
            'matricula'=>'required'
        ]);
        $val=Alumnos::where('users_id',$this->req->input('users_id'))
            ->where('niveles_has_anios_id',$this->req->input('niveles_has_anios_id'))
            ->get();
        if($val->count()){
            return response()->json(['msj'=>'El elemento ya fue creado anteriormente. No se crea uno nuevo.']);
        }
        $obj=new Alumnos;
        $obj->users_id=$this->req->input('users_id');
        $obj->acudiente=$this->req->input('acudiente');
        $obj->email_acudiente=$this->req->has('email_acudiente')? $this->req->input('email_acudiente') : '';
        $obj->niveles_has_anios_id=$this->req->input('niveles_has_anios_id');
        if($this->req->has('descripcion_pen')){
            $obj->descripcion_pen=$this->req->input('descripcion_pen');
        }else{
            $obj->descripcion_pen='';
        }
        if($this->req->has('descripcion_mat')){
            $obj->descripcion_mat=$this->req->input('descripcion_mat');
        }else{
            $obj->descripcion_mat='';
        }
        $obj->matricula=$this->req->input('matricula');
        $obj->pension=$this->req->input('pension');
        $obj->save();
        $rell=new Rellenador;
        $rell->autoLlenarAlumno($obj->id);
        $msj='Elemento Creado. Tabla=Alumnos, id='.$obj->id;
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
        $obj=Alumnos::with('users','niveles_has_anios.niveles','niveles_has_anios.anios')->findOrFail($id);
        $ev=new EventlogRegister;
        $msj='Consulta de elemento. Tabla=Alumnos, id='.$id;
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
        $ev->registro(1,'Intento de modificación. Tabla=Alumnos, id='.$id,$this->req->user()->id);
        $obj=Alumnos::findOrFail($id);
        $this->validate($this->req,[
            'pension'=>'required',
            'acudiente'=>'required',
            'matricula'=>'required',
            'niveles_has_anios_id'=>'required'
        ]);
        if($this->req->has('descripcion_pen')){
            $obj->descripcion_pen=$this->req->input('descripcion_pen');
        }else{
            $obj->descripcion_pen='';
        }
        if($this->req->has('descripcion_mat')){
            $obj->descripcion_mat=$this->req->input('descripcion_mat');
        }else{
            $obj->descripcion_mat='';
        }
        $obj->email_acudiente=$this->req->has('email_acudiente')? $this->req->input('email_acudiente') : '';
        $obj->acudiente=$this->req->input('acudiente');
        $cambio=false;
        if ( $obj->niveles_has_anios_id != $this->req->input('niveles_has_anios_id')) {
            $obj->niveles_has_anios_id=$this->req->input('niveles_has_anios_id');
            $cambio=true;
        }
        $obj->matricula=$this->req->input('matricula');
        $obj->pension=$this->req->input('pension');
        $obj->save();
        $msj='Modificación. Tabla=Alumnos, id='.$id;
        if ($cambio) {
            $res=new Borrador;
            $res->delAlumnoNotas($id); // Solo borra las notas
            $rell=new Rellenador;
            $rell->autoLlenarAlumno($obj->id);
        }
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
        $ev->registro(2,'Intento de eliminación. Tabla=Alumnos, id='.$id,$this->req->user()->id);
        $res=new Borrador;
        $res->delAlumnos($id);
        $msj='Borrado. Tabla=Alumnos, id='.$id;
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
        $obj=Alumnos::all();
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
        $obj=Alumnos::join('users','users_id','=','users.id')
            ->join('niveles_has_anios','niveles_has_anios_id','=','niveles_has_anios.id')
            ->join('niveles','niveles_has_anios.niveles_id','=','niveles.id')
            ->join('anios','niveles_has_anios.anios_id','=','anios.id')
            ->select('alumnos.*','users.name','users.lastname','users.identificacion')
            ->where('users.name','LIKE','%'.$info.'%')
            ->orWhere('users.lastname','LIKE','%'.$info.'%')
            ->orWhere('users.identificacion','LIKE','%'.$info.'%')
            ->orWhere('anios.anio','LIKE','%'.$info.'%')
            ->orWhere('niveles.nombre','LIKE','%'.$info.'%')
            ->orderBy('users.lastname','desc')
            ->with('users','niveles_has_anios.niveles','niveles_has_anios.anios.periodos')
            ->get();
        $msj='Busqueda. Tabla=Alumnos, letras='.$info;
        $ev=new EventlogRegister;
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
    }
}
