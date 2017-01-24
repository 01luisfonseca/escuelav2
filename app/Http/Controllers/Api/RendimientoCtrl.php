<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use App\Alumnos;

class RendimientoCtrl extends Controller
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
        $obj=Alumnos::where('users_id',$this->req->user()->id)
        	->orderBy('id','desc')
        	->with('niveles_has_anios.anios','niveles_has_anios.niveles')
        	->get();
        $ev=new EventlogRegister;
        $msj='Consulta registros de Notas del Alumno.';
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
    }

    /**
     * Muestra numero de registros
     *
     * @return \Illuminate\Http\Response
     */
    public function count(){
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $al=Alumnos::where('id',$id)->where('users_id',$this->req->user()->id)->get();
        $ev=new EventlogRegister;
        if (count($al)==0) {
        	$msj='El usuario intenta consultar un alumno diferente Se frena el proceso. Alumnos_id='.$id;
        	$ev->registro(1,$msj,$this->req->user()->id);
        	return response()->json(['msj'=>$msj]);
        }
        $obj=Alumnos::with([
        	'niveles_has_anios'=>function($query) use($id){
        		$query->with([
        			'materias_has_niveles'=>function($query) use($id){
        				$query->with([
        					'materias',
        					'materias_has_periodos'=>function($query) use($id){
        						$query->with([
        							'periodos',
        							'indicadores'=>function($query) use($id){
        								$query->with([
        									'tipo_nota'=>function($query) use($id){
        										$query->with([
        											'notas'=>function($query) use($id){
        												$query->where('alumnos_id',$id);
        											}
        										]);
        									}
        								]);
        							}
        						])->orderBy('periodos_id','asc');
        					}
        				]);
        			}
        		]);
        	}
        ])->findOrFail($id);
        $msj='Consulta el Alumno='.$id;
        $ev->registro(0,$msj,$this->req->user()->id);
        return $obj->toJson();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }
}
