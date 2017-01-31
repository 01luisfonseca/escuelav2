<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use App\Helpers\Borrador;
use Carbon\Carbon;
use Log;
use App\NivelesHasAnios;

class NivelesHasAniosCtrl extends Controller
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
        $obj=NivelesHasAnios::skip($ini)->take(50+$ini)->orderBy('updated_at','desc')->get();
        $ev=new EventlogRegister;
        $msj='Consulta registros. Tabla=NivelesHasAnios.';
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
        $ev->registro(1,'Intento de guardar en tabla=NivelesHasAnios.',$this->req->user()->id);
        $this->validate($this->req,[
            'anios_id'=>'required',
            'niveles_id'=>'required'
        ]);
        $elem=NivelesHasAnios::where('anios_id',$this->req->input('anios_id'))
        	->where('niveles_id',$this->req->input('niveles_id'))->get();
        if($elem->count()>0){
        	return response()->json(['msj'=>'No se puede almacenar un registro con el mismo año y nivel']);
        }
        $obj=new NivelesHasAnios;
        $obj->anios_id=$this->req->input('anios_id');
        $obj->niveles_id=$this->req->input('niveles_id');
        if ($this->req->has('empleados_id')) {
        	$obj->empleados_id=$this->req->input('empleados_id');
        }else{
        	$obj->empleados_id=0;
        }
        $obj->save();
        $msj='Elemento Creado. Tabla=NivelesHasAnios, id='.$obj->id;
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
        $obj=NivelesHasAnios::findOrFail($id);
        $ev=new EventlogRegister;
        $msj='Consulta de elemento. Tabla=NivelesHasAnios, id='.$id;
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
        $ev->registro(1,'Intento de modificación. Tabla=NivelesHasAnios, id='.$id,$this->req->user()->id);
        $obj=NivelesHasAnios::findOrFail($id);
        if ($this->req->has('anios_id')) {
        	$obj->anios_id=$this->req->input('anios_id');
        }
        if ($this->req->has('niveles_id')) {
        	$obj->niveles_id=$this->req->input('niveles_id');
        }
        if ($this->req->has('empleados_id')) {
        	$obj->empleados_id=$this->req->input('empleados_id');
        }
        $msj='Sin cambios en la tabla';
        if ($this->req->has('empleados_id') || $this->req->has('niveles_id') || $this->req->has('anios_id')) {
        	$obj->save();
        	$msj='Modificación. Tabla=NivelesHasAnios, id='.$id;
        	$ev->registro(1,$msj,$this->req->user()->id);
        }
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
        $ev->registro(2,'Intento de eliminación. Tabla=NivelesHasAnios, id='.$id,$this->req->user()->id);
        $res=new Borrador;
        $res->delNivelesHasAnios($id);
        $msj='Borrado. Tabla=NivelesHasAnios, id='.$id;
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
        $obj=NivelesHasAnios::all();
        return response()->json(['registros'=>$obj->count()]);
    }

    /**
     * Muestra numero de registros
     *
     * @return \Illuminate\Http\Response
     */
    public function nivelables(){
        $obj=NivelesHasAnios::join('anios','anios_id','=','anios.id')
            ->select('niveles_has_anios.*','anios.anio')
            ->with('anios','niveles')
            ->orderBy('anios.anio','desc')
            ->where('niveles_has_anios.id','>',0)
            ->get();
        return $obj->toJson();
    }

    /**
     * Devuelve los registros con ultimos pagos de todos los alumnos.
     *
     * @return \Illuminate\Http\Response
     */
    public function pagados($id){
        $obj=NivelesHasAnios::with(['niveles', 'alumnos'=>function($query){
            $query->with(['pago_matricula'=>function($query){
                $query->orderBy('created_at','desc')->take(1);
            },
            'pago_pension'=>function($query){
                $query->orderBy('mes_id','desc')->take(1);
            }])
                ->join('users','users_id','=','users.id')
                ->select('alumnos.*')
                ->orderBy('users.lastname','asc');
        }])
            ->join('niveles','niveles_id','=','niveles.id')
            ->select('niveles_has_anios.*')
            ->orderBy('niveles.nombre','asc')
            ->where('anios_id',$id)
            ->get();
        return $obj->toJson();
    }
}
