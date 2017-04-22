<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EventlogRegister;
use App\Helpers\AlumInd;
use App\NivelesHasAnios;
use App\MateriasHasPeriodos;
use Carbon\Carbon;
use Log;

class MttoCtrl extends Controller
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
     * Ejecuta la actualización de todos los indicadores.
     *
     * @return \Illuminate\Http\Response
     */
    public function indicador($id){
        $ev=new EventlogRegister;
        $msj='Actualización de indicadores por alumnos.';
        $ev->registro(2,$msj,$this->req->user()->id);
        $curso=NivelesHasAnios::with('alumnos','materias_has_niveles.materias_has_periodos.indicadores')->findOrFail($id);
        foreach ($curso->materias_has_niveles as $materia) {
            foreach ($materia->materias_has_periodos as $periodo) {
                foreach ($periodo->indicadores as $indic) {
                    foreach ($curso->alumnos as $alumno) {
                        $alumind=new AlumInd;
                        $alumind->addActProm($alumno->id, $indic->id, 'EXISTENTE');
                        Log::info('Alumno:'.$alumno->id.', Indicador: '.$indic->id);
                    }
                }
            }
        }
        return response()->json(['msj'=>'Bien']);
    }
    /**
     * Ejecuta la actualización de todos los periodos.
     *
     * @return \Illuminate\Http\Response
     */
    public function periodo($id, $idAlumno)
    {
        $ev=new EventlogRegister;
        $msj='Actualización de periodo por alumnos.';
        $ev->registro(2,$msj,$this->req->user()->id);
        $curso=NivelesHasAnios::with('materias_has_niveles.materias_has_periodos')->findOrFail($id);
        foreach ($curso->materias_has_niveles as $materia) {
            foreach ($materia->materias_has_periodos as $periodo) {
                $alumind=new AlumInd;
                $alumind->actPromPer($idAlumno, $periodo->id);
                $millis = time();
                Log::info('Alumno:'.$idAlumno.', Periodo: '.$periodo->id.', elapsed milis= ');
                Log:info($millis);
            }
        }
        return response()->json(['msj'=>'Bien']);
    }
    /**
     * Ejecuta la actualización de todos los periodos.
     *
     * @return \Illuminate\Http\Response
     */
    public function periodo2($id,$idAlumno)
    {
        $ev=new EventlogRegister;
        $msj='Actualización de periodo por alumnos.';
        $ev->registro(2,$msj,$this->req->user()->id);
        $materias_has_periodos=MateriasHasPeriodos::where('materias_has_niveles_id',$id)->get();
            foreach ($materias_has_periodos as $periodo) {
                $alumind=new AlumInd;
                $alumind->actPromPer($idAlumno, $periodo->id);
                $millis = time();
                Log::info('Alumno:'.$alumno->id.', Periodo: '.$periodo->id.', elapsed milis= ');
                Log:info($millis);
            }
        return response()->json(['msj'=>'Bien']);
    }
}