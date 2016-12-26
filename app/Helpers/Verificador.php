<?php

namespace App\Helpers;

use App\Helpers\Contracts\VerificadorContract;
use Illuminate\Http\Request;
use App\PagoPension;
use App\PagoMatricula;
use App\PagoOtros;

class Verificador implements VerificadorContract
{
    

    /**
     * Verifica el estado de existencia de una factura.
     *
     * @param  int  $tipoNId
     * @return texto de resultados
     */
    public function existeFactura($fac){
        $obj=PagoPension::where('numero_factura',$fac)->get();
        if($obj->count()==0){
            $obj=PagoMatricula::where('numero_factura',$fac)->get();
            if($obj->count()==0){
                $obj=PagoOtros::where('numero_factura',$fac)->get();
                if ($obj->count()==0) {
                    return false;
                }
            }
        }
        return true;
    }
}
