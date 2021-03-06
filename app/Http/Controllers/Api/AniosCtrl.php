<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use App\Helpers\Borrador;
use Carbon\Carbon;
use Log;
use App\Anios;
use App\Empleados;

class AniosCtrl extends Controller
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
        $obj=Anios::with(['periodos','niveles_has_anios'=>function($query){
            $query->with('niveles','materias_has_niveles.materias','materias_has_niveles.empleados.users.tipo_usuario');
        }])->skip($ini)->take(50+$ini)->orderBy('updated_at','desc')->get();
        $ev=new EventlogRegister;
        $msj='Consulta registros. Tabla=Anios.';
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
        $ev->registro(1,'Intento de guardar en tabla=Anios.',$this->req->user()->id);
        $this->validate($this->req,[
            'anio'=>'required'
        ]);
        $val=Anios::where('anio',$this->req->input('anio'));
        if($val->count()){
            return response()->json(['msj'=>'El elemento ya fue creado anteriormente. No se crea uno nuevo.']);
        }
        $obj=new Anios;
        $obj->anio=$this->req->input('anio');
        if ($this->req->has('descripcion')) {
            $obj->descripcion=$this->req->input('descripcion');
        }else{
            $obj->descripcion='';
        }
        $obj->save();
        $msj='Elemento Creado. Tabla=Anios, id='.$obj->id;
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
        $obj=Anios::with('niveles_has_anios.niveles')->findOrFail($id);
        $ev=new EventlogRegister;
        $msj='Consulta de elemento. Tabla=Anios, id='.$id;
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
        $ev->registro(1,'Intento de modificación. Tabla=Anios, id='.$id,$this->req->user()->id);
        $obj=Anios::findOrFail($id);
        $this->validate($this->req,[
            'anio'=>'required'
        ]);
        $obj=Anios::findOrFail($id);
        $obj->anio=$this->req->input('anio');
        if ($this->req->has('descripcion')) {
            $obj->descripcion=$this->req->input('descripcion');
        }else{
            $obj->descripcion='';
        }
        $obj->save();
        $msj='Modificación. Tabla=Anios, id='.$id;
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
        $ev->registro(2,'Intento de eliminación. Tabla=Anios, id='.$id,$this->req->user()->id);
        $res=new Borrador;
        $res->delAnios($id);
        $msj='Borrado. Tabla=Anios, id='.$id;
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
        $obj=Anios::all();
        return response()->json(['registros'=>$obj->count()]);
    }

    /**
     * Entrega registros en los cuales hay derecho de verlos, hasta materias_has_periodos
     *
     * @return \Illuminate\Http\Response
     */
    public function asignado(){
        $obj=Anios::with(['niveles_has_anios'=>function($query){
            $query->with(['niveles','materias_has_niveles'=>function($query){
                $query->with(['materias','materias_has_periodos'=>function($query){
                        $query->with('periodos');
                    }]);
                }]);
        }])->get();
        $arr=[];
        foreach ($obj as $anio) {
            $nivelArr=[];
            foreach ($anio->niveles_has_anios as $nivel) {
                $matArr=[];
                foreach ($nivel->materias_has_niveles as $mat) {
                    $perArr=[];
                    foreach ($mat->materias_has_periodos as $per) {
                        $perArr[]=[
                            'id'=>$per->id,
                            'nombre'=>$per->periodos->nombre
                        ];
                    }
                    if($this->req->user()->tipo_usuario_id>=5){
                        $matArr[]=[
                            'id'=>$mat->id, 
                            'nombre'=>$mat->materias->nombre, 
                            'periodos'=>$perArr
                        ];
                    }else{
                        $empleado=Empleados::where('users_id',$this->req->user()->id)->first();
                        if ($empleado) {
                            if ($empleado->id==$mat->empleados_id) {
                                $matArr[]=[
                                    'id'=>$mat->id, 
                                    'nombre'=>$mat->materias->nombre, 
                                    'periodos'=>$perArr
                                ];
                            }
                        }
                    }
                }
                if (count($matArr)>0) {
                    $nivelArr[]=[
                        'id'=>$nivel->id,
                        'nombre'=>$nivel->niveles->nombre,
                        'materias'=>$matArr
                    ];
                }
            }
            if (count($nivelArr)) {
                $arr[]=[
                    'id'=>$anio->id,
                    'anio'=>$anio->anio,
                    'niveles'=>$nivelArr
                ];
            }
        }
        $col= collect($arr);
        return $col->toJson();
    }
}
