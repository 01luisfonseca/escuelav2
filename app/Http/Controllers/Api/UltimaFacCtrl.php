<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use App\PagoPension;
use App\PagoMatricula;
use App\PagoOtros;

class UltimaFacCtrl extends Controller
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
        $ptP=PagoPension::select('numero_factura')->where('id','>',0)->orderBy('numero_factura','desc')->first();
        $ptM=PagoMatricula::select('numero_factura')->where('id','>',0)->orderBy('numero_factura','desc')->first();        
        $ptO=PagoOtros::select('numero_factura')->where('id','>',0)->orderBy('numero_factura','desc')->first();
        $pP=$ptP? $this->sacaNumeros($ptP->numero_factura) : 0;
        $pM=$ptM? $this->sacaNumeros($ptM->numero_factura) : 0;
        $pO=$ptO? $this->sacaNumeros($ptO->numero_factura) : 0;
        $mayor=$pP >= $pM ? ($pP >= $pO ? $ptP : $ptO) : ($pM >= $pO ? $ptM : $ptO);               
        $ev=new EventlogRegister;
        $msj='Consulta registros para determinar la ultima factura generada.';
        $ev->registro(0,$msj,$this->req->user()->id);
        if ($mayor) {
            return $mayor->toJson();
        }
        return response()->json(["numero_factura"=>"0"]);
    }

    /**
     * Saca los números en la factura para calcular el mayor
     *
     * @return \Illuminate\Http\Response
     */
    private function sacaNumeros($text){
    	$number = preg_replace("/[^0-9]/", '', $text);
		return (int)$number;
    }
}
