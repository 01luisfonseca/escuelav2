<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use App\Helpers\AlumInd;
use Carbon\Carbon;
use Log;
use App\PagoGasto;
use App\PagoPension;
use App\PagoMatricula;
use App\PagoOtros;
use App\PagoSalario;

class IngreyEgreCtrl extends Controller
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
    public function index($anio)
    {
        $ev=new EventlogRegister;
        $msj='Se hace consulta de registros para la relación de ingresos y gastos.';
        $ev->registro(0,$msj,$this->req->user()->id);
        return $this->crearObjeto($anio)->toJson();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $user
     * @return \Illuminate\Http\Response
     */
    public function show($anio,$mes)
    {
        $ev=new EventlogRegister;
        $msj='Generación de exportación'.$id;
        $ev->registro(0,$msj,$this->req->user()->id);
        return '';
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
        $obj=PagoGasto::all();
        return response()->json(['registros'=>$obj->count()]);
    }

    /**
     * Retorna una colección con los objetos calculados
     *
     * @return \Illuminate\Http\Response
     */
     private function crearObjeto($anio){
    	$from=Carbon::create($anio, 1, 1, 0, 0, 0);
    	$to=Carbon::create($anio+1, 1, 1, 0, 0, 0);
    	$pG=PagoGasto::select('pago_gasto.valor','pago_gasto.created_at')
    		->whereBetween('created_at', [$from, $to])
    		->get();
    	$pP=PagoPension::select('pago_pension.valor','pago_pension.created_at')
    		->whereBetween('created_at', [$from, $to])
    		->get();
    	$pM=PagoMatricula::select('pago_matricula.valor','pago_matricula.created_at')
    		->whereBetween('created_at', [$from, $to])
    		->get();
    	$pO=PagoOtros::select('pago_otro.valor','pago_otro.created_at')
    		->whereBetween('created_at', [$from, $to])
    		->get();
    	$pS=PagoSalario::select('pago_salario.salario_pagado','pago_salario.created_at')
    		->whereBetween('created_at', [$from, $to])
    		->get();
    	$porMes=[];
    	$ingresos=0;
    	$gastos=0;
    	for ($i=1; $i <= 12; $i++) { 
    		$ingreso=$this->acumuladorMes($pP,$i);
    		$ingreso+=$this->acumuladorMes($pM,$i);
    		$ingreso+=$this->acumuladorMes($pO,$i);
    		$ingresos+=$ingreso;
    		$gasto=$this->acumuladorMes($pG,$i);
    		$gasto+=$this->acumuladorMes($pS,$i);
    		$gastos+=$gasto;
    		array_push($porMes,['name'=>['Mes','Ingreso'],'value'=>[$this->resMes($i),$ingreso]],['name'=>['Mes','Gasto'],'value'=>[$this->resMes($i),$gasto]]);
    	}
    	$gen=['name'=>['Ingreso','Gasto'],'value'=>[$ingresos, $gastos], 'Meses'=>$porMes];
    	return collect($gen);
    }
    private function resMes($id){
    	switch ($id) {
    		case 1:
    			return 'Enero';
    			break;
			case 2:
    			return 'Febrero';
    			break;
    		case 3:
    			return 'Marzo';
    			break;
    		case 4:
    			return 'Abril';
    			break;
    		case 5:
    			return 'Mayo';
    			break;
    		case 6:
    			return 'Junio';
    			break;
    		case 7:
    			return 'Julio';
    			break;
    		case 8:
    			return 'Agosto';
    			break;
    		case 9:
    			return 'Septiembre';
    			break;
    		case 10:
    			return 'Octubre';
    			break;
    		case 11:
    			return 'Noviembre';
    			break;
    		case 12:
    			return 'Diciembre';
    			break;
    	}
    }
    private function acumuladorMes($col,$mes){
    	$acum=0;
    	foreach ($col as $c) {
    		$dt=new Carbon($c->created_at);
    		if ($dt->month==$mes) {
    			try{
    				$acum += floatval($c->valor);
    			}catch (Exception $e) {
    				$acum += floatval($c->salario_pagado);
    			}
    		}
    	}
    	return $acum;
    }
}
