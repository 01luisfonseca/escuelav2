<?php

namespace App\Http\Middleware;

use Closure;

use App\Authdevice;

class CheckSerial
{
    /**
     * Handle an incoming request. Verifica si existe el serial del dispositivo en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $serial=$request->serial;
        $obj=Authdevice::where('serial',$serial)->first();
        if (is_null($obj)) {
            return response('Unauthorized.', 401);
        }
        if($obj->estado==0 || $obj->estado==false){
            return response('Unabled.', 401);
        }
        return $next($request);
    }
}
