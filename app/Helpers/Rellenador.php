<?php

namespace App\Helpers;

use App\Helpers\Contracts\RellenadorContract;
use Illuminate\Http\Request;
use App\MateriasHasPeriodos;
use App\MateriasHasNiveles;
use App\Periodos;


class Rellenador implements RellenadorContract
{
    public function PeriodosEnMateriasMHP($perId){
        $origen=Periodos::with('anios.niveles_has_anios.materias_has_niveles')->find($perId);
        $comparado=$origen->anios->niveles_has_anios->materias_has_niveles;
        foreach ($comparado as $val) {
            $obj=new MateriasHasPeriodos;
            $obj->periodos_id=$perId;
            $obj->materias_has_niveles_id=$val->id;
            $obj->save();
            $res.=' MHP ID: '.$obj->id.', PER ID: '.$val->id.', MHN: '.$matId.'. ';
        }
        return $res;
    }

    public function MateriasEnPeriodosMHP($matId){
        $origen=MateriasHasNiveles::with('niveles_has_anios.anios.periodos')->find($matId);
        $comparado=$origen->niveles_has_anios->anios->periodos;
        $res='Se ha rellenado MateriasHasPeriodos con: ';
        foreach ($comparado as $val) {
            $obj=new MateriasHasPeriodos;
            $obj->materias_has_niveles_id=$matId;
            $obj->periodos_id=$val->id;
            $obj->save();
            $res.=' MHP ID: '.$obj->id.', PER ID: '.$val->id.', MHN: '.$matId.'. ';
        }
        return $res;
    }
}
