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
        $porMesIngreso=[];
    	$porMesGasto=[];
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
    		array_push($porMesIngreso,
                ['label'=>$this->resMes($i),'y'=>$ingreso]
            );
            array_push($porMesGasto,
                ['label'=>$this->resMes($i),'y'=>$gasto]
            );
    	}
    	$gen=[
            'Ingresos'=>['valores'=>$porMesIngreso,'total'=>$ingresos],
            'Gastos'=>['valores'=>$porMesGasto,'total'=> $gastos]
        ];
    	return collect($gen);
    }
    private function resMes($id){
    	switch ($id) {
    		case 1:
    			return 'Ene';
    			break;
			case 2:
    			return 'Feb';
    			break;
    		case 3:
    			return 'Mar';
    			break;
    		case 4:
    			return 'Abr';
    			break;
    		case 5:
    			return 'May';
    			break;
    		case 6:
    			return 'Jun';
    			break;
    		case 7:
    			return 'Jul';
    			break;
    		case 8:
    			return 'Ago';
    			break;
    		case 9:
    			return 'Sep';
    			break;
    		case 10:
    			return 'Oct';
    			break;
    		case 11:
    			return 'Nov';
    			break;
    		case 12:
    			return 'Dic';
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
