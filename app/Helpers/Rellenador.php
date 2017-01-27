<?php

namespace App\Helpers;

use App\Helpers\Contracts\RellenadorContract;
use Illuminate\Http\Request;
use App\Helpers\AlumInd;
use App\NivelesHasAnios;
use App\Alumnos;
use App\MateriasHasPeriodos;
use App\MateriasHasNiveles;
use App\Periodos;
use App\TipoNota;
use App\Notas;

class Rellenador implements RellenadorContract
{
    //Rellena las notas de un alumno despues de crearlo
    public function autoLlenarAlumno($id){
        $alumno=Alumnos::find($id);
        $notasNivel=NivelesHasAnios::with(['materias_has_niveles'=>function($query){
                $query->with(['materias','materias_has_periodos'=>function($query){
                    $query->with(['periodos','indicadores.tipo_nota.notas']);
                }]);
            }])
            ->find($alumno->niveles_has_anios_id);
        $resultado=[];
        if ($notasNivel->materias_has_niveles->count()>0) {
        foreach ($notasNivel->materias_has_niveles as $materia) {
            // en cada materia
            if ($materia->materias_has_periodos->count()>0) {
            foreach ($materia->materias_has_periodos as $periodo) {
                // En cada periodo
                if ($periodo->indicadores->count()>0) {
                foreach ($periodo->indicadores as $indicador) {
                    // En cada indicador
                    $notaAdd=false;
                    if ($indicador->tipo_nota->count()>0) {
                    foreach ($indicador->tipo_nota as $tipo) {
                        // En cada tipo
                        $encontrado=false;
                        if ($tipo->notas->count()>0) {
                        foreach ($tipo->notas as $nota) {
                            // En cada nota, si lo encuentra, pone $encontrado a true, de lo contrario false
                            if($nota->alumnos_id==$alumno->id){
                                $encontrado=true;
                                break;
                            }
                        }
                        // Si no se encontró al alumno, se crea una nota vacía.
                        if(!$encontrado){
                            $notaAdd=true;
                            $obj=new Notas;
                            $obj->tipo_nota_id=$tipo->id;
                            $obj->calificacion=0;
                            $obj->alumnos_id=$alumno->id;
                            $obj->save();
                            Log::info('Creada nota de alumno ID:'+$alumno->id+'Con el tipo de nota ID:'+$tipo->id);
                            $resultado[]=[
                                'materia_id'=>$materia->id,
                                'materia_name'=>$materia->materias->nombre_materia,
                                'periodo_id'=>$periodo->id,
                                'periodo_name'=>$periodo->periodos->nombre_periodo,
                                'indicador_id'=>$indicador->id,
                                'indicador_name'=>$indicador->nombre,
                                'tipo_nota_id'=>$tipo->id,
                                'tipo_nota_name'=>$tipo->nombre,
                                'alumnos_id'=>$alumno->id
                                ];  
                        }
                        }
                    }
                    }
                    if ($notaAdd) {
                        $ind=new AlumInd;
                        $ind->addActProm($alumno->id,$indicador_id);
                    }
                }
                }
            }
            }
        }
        }
        // Devuelve una colección con al menos el mensaje de estado.
        $col=collect([$resultado]);
        return $col->toJson();
    }

    public function PeriodosEnMateriasMHP($perId){
        $origen=Periodos::with('anios.niveles_has_anios.materias_has_niveles')->find($perId);
        $niveles=$origen->anios->niveles_has_anios;
        $res='Se ha rellenado MateriasHasPeriodos con: ';
        foreach ($niveles as $nivel) {
        $comparado=$nivel->materias_has_niveles;
        foreach ($comparado as $val) {
            $obj=new MateriasHasPeriodos;
            $obj->periodos_id=$perId;
            $obj->materias_has_niveles_id=$val->id;
            $obj->save();
            $res.=' MHP ID: '.$obj->id.', PER ID: '.$perId.', MHN: '.$val->id.'. ';
        }
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

    /**
     * Rellena notas en tipos de nota.
     *
     * @param  int  $tipoNId
     * @return texto de resultados
     */
    public function TipoNotaEnNotas($tipoNId){
        $origen=TipoNota::with(['notas',
            'indicadores.materias_has_periodos.materias_has_niveles.niveles_has_anios.alumnos'])
            ->find($tipoNId);
        $alumnos=$origen->indicadores->materias_has_periodos->materias_has_niveles->niveles_has_anios->alumnos;
        $comparado=$origen->notas;
        $res='Se han rellenado Notas con: ';
        foreach ($alumnos as $val) {
            $encontrado=false;
            foreach ($origen->notas as $nota) {
                if ($nota->alumnos_id == $val->id) {
                    $encontrado=true;
                }
            }
            if (!$encontrado) {
                $obj=new Notas;
                $obj->alumnos_id=$val->id;
                $obj->tipo_nota_id=$tipoNId;
                $obj->calificacion=0;
                $obj->save();
                $res.='; Notas ID: '.$obj->id.', Alumnos ID: '.$val->id.', TipoNota ID: '.$tipoNId.', Cal: 0';
                // Actualiza el promedio si hay modificaciones
                $alumind=new AlumInd;
                $alumind->addActProm($obj->alumnos_id,$origen->indicadores->id);
            }
        }
        return $res;
    }
}
