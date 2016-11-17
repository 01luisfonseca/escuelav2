<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use Carbon\Carbon;
use App\User;
use Log;

class UserCtrl extends Controller
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
        $obj=User::where('id','>',2)->with('tipo_usuario')->orderBy('updated_at')->skip($ini)->take(50+$ini)->get();
        $ev=new EventlogRegister;
        $msj='Consulta registros de usuarios.';
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
        $ev->registro(1,'Intento de guardar usuario.',$this->req->user()->id);
        $this->validate($this->req,[
            'name'=>'required',
            'lastname'=>'required',
            'email'=>'required | unique:users',
            'password'=>'required',
            'identificacion'=>'required | unique:users',
            'birday'=>'required',
            'telefono'=>'required',
            'direccion'=>'required',
            'tipo_sangre'=>'required',
            'tipo_usuario_id'=>'required',
            'estado'=>'required'
        ]);
        $date=new Carbon($this->req->input('birday'));
        $obj=new User;
        $obj->name=$this->req->input('name');
        $obj->lastname=$this->req->input('lastname');
        $obj->email=$this->req->input('email');
        $obj->identificacion=$this->req->input('identificacion');
        $obj->birday=$date;
        $obj->telefono=$this->req->input('telefono');
        $obj->direccion=$this->req->input('direccion');
        $obj->tipo_sangre=$this->req->input('tipo_sangre');
        $obj->estado=$this->req->input('estado');
        $obj->tarjeta=$this->req->input('tarjeta');
        $obj->tipo_usuario_id=$this->req->input('tipo_usuario_id');
        $obj->password=bcrypt($this->req->input('password'));
        $obj->save();

        //$obj=User::create($this->req->all());
        $msj='Usuario creado con id '.$obj->id;
        $ev->registro(1,$msj,$this->req->user()->id);
        return response()->json(['msj'=>$msj]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $user
     * @return \Illuminate\Http\Response
     */
    public function show($user)
    {
        $obj=User::with('tipo_usuario')->where('id','>',2)->findOrFail($user);
        $ev=new EventlogRegister;
        $msj='Consulta el usuario id='.$user;
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($user)
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
    public function update($user)
    {
        $ev=new EventlogRegister;
        $ev->registro(1,'Intento de actualizar usuario id='.$user,$this->req->user()->id);
        $obj=User::findOrFail($user);
        if($this->req->has('password')){
            $obj->password=bcrypt($this->req->input('password'));
        }else{
            $this->validate($this->req,[
                'name'=>'required',
                'lastname'=>'required',
                'email'=>'required',
                'identificacion'=>'required',
                'birday'=>'required',
                'telefono'=>'required',
                'direccion'=>'required',
                'tipo_sangre'=>'required',
                'tipo_usuario_id'=>'required',
                'estado'=>'required'
            ]);
            $date=new Carbon($this->req->input('birday'));
            $obj->birday=$date;
            $obj->name=$this->req->input('name');
            $obj->lastname=$this->req->input('lastname');
            $obj->email=$this->req->input('email');
            $obj->identificacion=$this->req->input('identificacion');
            $obj->tarjeta=$this->req->input('tarjeta');
            $obj->telefono=$this->req->input('telefono');
            $obj->direccion=$this->req->input('direccion');
            $obj->tipo_sangre=$this->req->input('tipo_sangre');
            $obj->estado=$this->req->input('estado');
            $obj->tipo_usuario_id=$this->req->input('tipo_usuario_id');
            $obj->save();
        }
        $msj='Modifica la información del usuario id='.$user;
        $ev->registro(1,$msj,$this->req->user()->id);
        return response()->json(['msj'=>$msj]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($user)
    {
        return response()->json($this->req->all());
        $ev=new EventlogRegister;
        $ev->registro(2,'Intento de eliminar usuario. id='.$user,$this->req->user()->id);
        if ($user==1) {
            return response()->json(['msj'=>'No se puede eliminar al administrador']);
        }
        $obj=User::findOrFail($user);
        $obj->delete();
        $msj='Se ha borrado el registro '.$obj->id;
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
        $obj=User::all();
        return response()->json(['registros'=>$obj->count()]);
    }

    /**
     * Busca los objetos que coincidan.
     *
     * @param  int  $info
     * @return \Illuminate\Http\Response
     */
    public function search($info) // Falta esto
    {
        $obj=User::with('tipo_usuario')
            ->select(
                'users.id',
                'users.name',
                'users.lastname',
                'users.identificacion',
                'users.updated_at',
                'users.estado',
                'users.telefono',
                'users.email',
                'users.tarjeta',
                'tipo_usuario.nombre as tnombre'
                )
            ->join('tipo_usuario','tipo_usuario_id','=','tipo_usuario.id')
            ->where('users.id','!=','1') // El administrador nunca aparece
            ->where('identificacion','LIKE','%'.$info.'%')
            ->orWhere('name','LIKE','%'.$info.'%')
            ->orWhere('lastname','LIKE','%'.$info.'%')
            ->orWhere('email','LIKE','%'.$info.'%')
            ->orWhere('telefono','LIKE','%'.$info.'%')
            ->orWhere('tipo_usuario.nombre','LIKE','%'.$info.'%')
            ->orWhere('tarjeta','LIKE','%'.$info.'%')
            ->orderBy('lastname','desc')
            ->get();
        $msj='Se han buscado los registros con letras: '.$info;
        $ev=new EventlogRegister;
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
    }

    /**
     * Actualiza el estado del usuario.
     *
     * @param  int  $info
     * @return \Illuminate\Http\Response
     */
    public function modEstado($user,$status){
        if (!preg_match("/^[[:digit:]]+$/", $status)) {
            return response()->json(['msj'=>'No es entero']);
        }
        $ev=new EventlogRegister;
        if ($user==1) {
            return response()->json(['msj'=>'No se puede deshabilitar al administrador']);
        }
        $obj=User::findOrFail($user);
        $obj->estado=$status;
        $obj->save();
        $msj='Se ha cambiado el status del usuario id= '.$user.' a estado= '.$status;
        $ev->registro(2,$msj,$this->req->user()->id);
        return response()->json(['msj'=>$msj]);
    }

    /**
     * Muestra usuarios que pueden ser empleables.
     *
     * @param  Ninguno
     * @return \Illuminate\Http\Response
     */
    public function userEmpleable(){
        $obj=User::where('tipo_usuario_id','>',2)
            ->where('id','>',2)
            ->orderBy('lastname','desc')
            ->get();
        $ev=new EventlogRegister;
        $msj='Consulta de empleables. Tabla=users';
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
    }
}
