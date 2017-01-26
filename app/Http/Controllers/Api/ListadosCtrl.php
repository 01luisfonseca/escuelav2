<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use App\Helpers\Borrador;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Log;
use App\Anios;
use App\Alumnos;
use App\Generales;
use App\NivelesHasAnios;

class ListadosCtrl extends Controller
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
     * Muestra Anios y NivelesHasAnios.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($ini=0)
    {
        $obj=Anios::with(['niveles_has_anios'=>function($query){
        	$query->with('niveles')
        		->join('niveles','niveles_has_anios.niveles_id','=','niveles.id')
        		->select('niveles_has_anios.*')
        		->orderBy('niveles.nombre','asc');
        }])
            ->orderBy('anio','desc')
            ->skip($ini)->take(50+$ini)->get();
        $ev=new EventlogRegister;
        $msj='Consulta registros para listados. Tabla=Anios.';
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
    }

    /**
     * Muestra los alumnos de un NivelesHasAnios.
     *
     * @param  int  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $obj=Alumnos::with('users')
        	->join('users','alumnos.users_id','=','users.id')
        	->select('alumnos.*','users.name','users.lastname','users.identificacion','users.direccion','users.telefono')
        	->orderBy('users.lastname', 'asc')
        	->where('niveles_has_anios_id',$id)
        	->get();
        $ev=new EventlogRegister;
        $msj='Consulta de registros para listados. Tabla=Alumnos, niveles_has_anios_id='.$id;
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
    }

    /////////////////////////////////////////////
    /////////// FUNCIONES ADICIONALES ///////////
    /////////////////////////////////////////////

    /**
     * Exporta a excel el nivel
     *
     * @return \Illuminate\Http\Response
     */
    public function exportarAlumnos($id){
    	$org=Generales::where('nombre','Organización')->first();
    	$nit=Generales::where('nombre','NIT')->first();
        $nivel=NivelesHasAnios::with('niveles')->find($id);
        $objeto=Alumnos::select('alumnos.*','users.name','users.lastname','users.identificacion','users.direccion','users.telefono')
            ->join('users','alumnos.users_id','=','users.id')
            ->where('niveles_has_anios_id','=',$id)
            ->orderBy('users.lastname','asc')
            ->get();
        if($objeto->count()>0){
            Excel::create('Alumnos por nivel', function($excel) use ($org,$nit,$objeto,$nivel){
                $excel->sheet('Nivel', function($sheet) use ($org,$nit,$objeto,$nivel){ 
                    $sheet->row(1,[$org->valor]);
                    $sheet->row(2,['LISTADO DE ALUMNOS POR NIVEL']);
                    $sheet->row(3,['NIT. ',(string)$nit->valor]);
                    $sheet->row(4,['NIVEL:',$nivel->niveles->nombre]);
                    $sheet->row(5,['']);
                    $sheet->row(6,[
                        'IDENTIFICACION',
                        'APELLIDOS',
                        'NOMBRES',
                        'DIRECCION',
                        'TELEFONO',
                        'ACUDIENTE',
                        'EMAIL ACUDIENTE'
                    ]);
                    $contador=7;//El siguiente despues de los títulos
                    foreach ($objeto as $value) {
                        $sheet->row($contador,[
                            $value->identificacion,
                            $value->lastname,
                            $value->name,
                            $value->direccion,
                            $value->telefono,
                            $value->acudiente,
                            $value->email_acudiente
                        ]);
                        $contador++;
                    }
                });
            })->export('xls');
        }
    }
}
