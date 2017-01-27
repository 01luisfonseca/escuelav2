<?php

namespace App\Helpers;

use App\Helpers\Contracts\BorradorContract;
use Illuminate\Http\Request;
use App\Alumnos;
use App\Anios;
use App\Asiserved;
use App\Authdevice;
use App\Empleados;
use App\Eventlog;
use App\Generales;
use App\Indicadores;
use App\Periodos;
use App\Materias;
use App\MateriasHasNiveles;
use App\MateriasHasPeriodos;
use App\Meses;
use App\Newasistencia;
use App\Matasistencia;
use App\Niveles;
use App\NivelesHasAnios;
use App\TipoNota;
use App\Notas;
use App\PagoMatricucula;
use App\PagoOtros;
use App\PagoPension;
use App\PagoSalario;
use App\Pension;
use App\Tarjetas;
use App\TipoUsuario;
use App\AlumnosHasIndicadores;


class Borrador implements BorradorContract
{

    public function getLimpiarHuerfanos(){
        $resultados='Resultados de limpieza de huerfanos: ';
        $resultados.=' '.$this->eliminarAlumnosHuerfanos().' alumnos eliminados, ';
        $resultados.=' '.$this->eliminarEmpleadosHuerfanos().' empleados eliminados, ';
        $resultados.=' '.$this->eliminarNivelesHasAniosHuerfanos().' niveles-años eliminadas, ';
        $resultados.=' '.$this->eliminarMateriasHasNivelesHuerfanos().' niveles-materias eliminadas, ';
        $resultados.=' '.$this->eliminarMateriasHasPeriodosHuerfanos().' niveles-materias-periodos eliminados, ';
        $resultados.=' '.$this->eliminarNewasistenciasHuerfanos().' asistencias globales eliminadas, ';
        $resultados.=' '.$this->eliminarMatasistenciasHuerfanos().' asistencias en materias eliminadas, ';
        $resultados.=' '.$this->eliminarIndicadoresHuerfanos().' indicadores eliminadas, ';
        $resultados.=' '.$this->eliminarTiposHuerfanos().' tipos de nota eliminadas. ';
        //$resultados.=' '.$this->eliminarNotasHuerfanos().' notas eliminadas. ';
        
        return $resultados;
    }

    public function getLimpiarHuerfanosLiviano(){
        $resultados=$this->eliminarAlumnosHuerfanos();
        $resultados+=$this->eliminarEmpleadosHuerfanos();
        $resultados+=$this->eliminarNivelesHasAniosHuerfanos();
        $resultados+=$this->eliminarMateriasHasNivelesHuerfanos();
        $resultados+=$this->eliminarMateriasHasPeriodosHuerfanos();
        $resultados+=$this->eliminarNewasistenciasHuerfanos();
        $resultados+=$this->eliminarMatasistenciasHuerfanos();
        $resultados+=$this->eliminarIndicadoresHuerfanos();
        $resultados+=$this->eliminarTiposHuerfanos();
        $resultados+=$this->eliminarNotasHuerfanos();
        return $resultados;
    }

    public function eliminarNivelesHasAniosHuerfanos(){
        $elementos=NivelesHasAnios::all();
        $marcado=array();
        $eliminados=0;
        foreach ($elementos as $elemento) {
            if (
                !$this->hayAnio($elemento->anio_id) ||
                !$this->hayNivel($elemento->niveles_id)
                ) 
            {
                $marcado[]=$elemento->id;
            }
        }
        $marcado=array_unique($marcado);
        $eliminados=count($marcado);
        if($eliminados>0){
            foreach ($marcado as $seleccionado) {
                $eliminado=NivelesHasAnios::findOrFail($seleccionado);
                $eliminado->delete();
            }
        }
        return $eliminados;
    }//Verificado

    public function eliminarMateriasHasNivelesHuerfanos(){
        $elementos=MateriasHasNiveles::all();
        $marcado=array();
        $eliminados=0;
        foreach ($elementos as $elemento) {
            if (
                !$this->hayMateria($elemento->materias_id) ||
                !$this->hayNivelesHasAnios($elemento->niveles_has_anios_id)
                ) 
            {
                $marcado[]=$elemento->id;
            }
            if ($elemento->empleados_id!=0) {
                if(!$this->hayEmpleado($elemento->empleados_id)){
                    $especial=MateriasHasNiveles::findOrFail($elemento->id);
                    $especial->empleados_id=0;
                    $especial->save();
                }
            }
        }
        $marcado=array_unique($marcado);
        $eliminados=count($marcado);
        if($eliminados>0){
            foreach ($marcado as $seleccionado) {
                $eliminado=MateriasHasNiveles::findOrFail($seleccionado);
                $eliminado->delete();
            }
        }
        return $eliminados;
    }//Verificado

    public function eliminarMateriasHasPeriodosHuerfanos(){
        $elementos=MateriasHasPeriodos::all();
        $marcado=array();
        $eliminados=0;
        foreach ($elementos as $elemento) {
            if (
                !$this->hayMateriasHasNiveles($elemento->materias_has_niveles_id) ||
                !$this->hayPeriodo($elemento->periodos_id)
                ) 
            {
                $marcado[]=$elemento->id;
            }
        }
        $marcado=array_unique($marcado);
        $eliminados=count($marcado);
        if($eliminados>0){
            foreach ($marcado as $seleccionado) {
                $eliminado=MateriasHasPeriodos::findOrFail($seleccionado);
                $eliminado->delete();
            }
        }
        return $eliminados;
    }//Verificado

    public function eliminarNewasistenciasHuerfanos(){
        $elementos=Newasistencia::all();
        $marcado=array();
        $eliminados=0;
        foreach ($elementos as $elemento) {
            if (
                !$this->hayPeriodos($elemento->periodos_id) ||
                !$this->hayAlumno($elemento->alumnos_id)
                ) 
            {
                $marcado[]=$elemento->id;
            }
        }
        $marcado=array_unique($marcado);
        $eliminados=count($marcado);
        if($eliminados>0){
            foreach ($marcado as $seleccionado) {
                $eliminado=Newasistencia::findOrFail($seleccionado);
                $eliminado->delete();
            }
        }
        return $eliminados;
    }//Verificado

    public function eliminarMatasistenciasHuerfanos(){
        $elementos=Matasistencia::all();
        $marcado=array();
        $eliminados=0;
        foreach ($elementos as $elemento) {
            if (
                !$this->hayMateriasHasPeriodos($elemento->materias_has_periodos_id) ||
                !$this->hayAlumno($elemento->alumnos_id)
                ) 
            {
                $marcado[]=$elemento->id;
            }
        }
        $marcado=array_unique($marcado);
        $eliminados=count($marcado);
        if($eliminados>0){
            foreach ($marcado as $seleccionado) {
                $eliminado=Matasistencia::findOrFail($seleccionado);
                $eliminado->delete();
            }
        }
        return $eliminados;
    }//Verificado

    public function eliminarIndicadoresHuerfanos(){
        $elementos=Indicadores::all();
        $marcado=array();
        $eliminados=0;
        foreach ($elementos as $elemento) {
            if (
                !$this->hayMateriasHasPeriodos($elemento->niveles_has_periodos_id)
                ) 
            {
                $marcado[]=$elemento->id;
            }
        }
        $marcado=array_unique($marcado);
        $eliminados=count($marcado);
        if($eliminados>0){
            foreach ($marcado as $seleccionado) {
                $eliminado=Indicadores::findOrFail($seleccionado);
                $eliminado->delete();
            }
        }
        return $eliminados;
    }

    public function eliminarTiposHuerfanos(){
        $elementos=TipoNota::all();
        $marcado=array();
        $eliminados=0;
        foreach ($elementos as $elemento) {
            if (
                !$this->hayIndicadores($elemento->indicadores_id)
                ) 
            {
                $marcado[]=$elemento->id;
            }
        }
        $marcado=array_unique($marcado);
        $eliminados=count($marcado);
        if($eliminados>0){
            foreach ($marcado as $seleccionado) {
                $eliminado=TipoNota::findOrFail($seleccionado);
                $eliminado->delete();
            }
        }
        return $eliminados;
    }

    public function eliminarNotasHuerfanos($rango=500){
        $obj=Notas::select('id')->where('id','>',0)->orderBy('id','desc')->get();
        $registros=$obj[0]->id;
        $numero=0;
        for ($i=0; $i*$rango < $registros; $i++) { 
            $numero+=$this->eliminarNotasHuerfanosPorRango($i*$rango,($i+1)*$rango);
        }
        return $numero;
    }

    public function eliminarNotasHuerfanosPorRango($idBajo=1,$idAlto=200){
        $elementos=Notas::where('id','<',$idAlto)->where('id','>=',$idBajo)->get();
        $marcado=array();
        $eliminados=0;
        foreach ($elementos as $elemento) {
            if (
                !$this->hayTipoNotas($elemento->tipo_nota_id) ||
                !$this->hayAlumno($elemento->alumnos_id)
                ) 
            {
                $marcado[]=$elemento->id;
            }
        }
        $marcado=array_unique($marcado);
        $eliminados=count($marcado);
        if($eliminados>0){
            foreach ($marcado as $seleccionado) {
                $eliminado=Notas::findOrFail($seleccionado);
                $eliminado->delete();
            }
        }
        return $eliminados;
    }

    public function eliminarAlumnosHuerfanos(){
        $elementos=Alumnos::all();
        $marcado=array();
        $eliminados=0;
        foreach ($elementos as $elemento) {
            if (
                !$this->hayUsuario($elemento->users_id) ||
                !$this->hayNivelesHasAnios($elemento->niveles_has_anios_id)
                ) 
            {
                $marcado[]=$elemento->id;
            }
        }
        $marcado=array_unique($marcado);
        $eliminados=count($marcado);
        if($eliminados>0){
            foreach ($marcado as $seleccionado) {
                $eliminado=Alumnos::findOrFail($seleccionado);
                $eliminado->delete();
            }
        }
        return $eliminados;
    }

    public function eliminarEmpleadosHuerfanos(){
        $elementos=Empleados::all();
        $marcado=array();
        $eliminados=0;
        foreach ($elementos as $elemento) {
            if (
                !$this->hayUsuario($elemento->users_id)
                ) 
            {
                $marcado[]=$elemento->id;
            }
        }
        $marcado=array_unique($marcado);
        $eliminados=count($marcado);
        if($eliminados>0){
            foreach ($marcado as $seleccionado) {
                $eliminado=Alumnos::findOrFail($seleccionado);
                $eliminado->delete();
            }
        }
        return $eliminados;
    }

    // Eliminar registros en cadena.

    public function delUser($id){
        $elem=User::with('alumnos','empleados')->findOrFail($id);
        if($this->esUtil($elem)){
            foreach ($elem->alumnos as $hijo) {
                $this->delAlumnos($hijo->id); // Borrar hijos
            }
            foreach ($elem->empleados as $hijo) {
                $this->delEmpleados($hijo->id); // Borrar hijos
            }
            $elem->delete(); // Borrar el elemento en si
            return true;
        }
        return false;
    }

    public function delAlumnos($id){
        $elem=Alumnos::with('pago_matricula','pago_pension','pago_otro','notas','newasistencia', 'matasistencia','alumnos_has_indicadores')->findOrFail($id);
        if($this->esUtil($elem)){
            foreach ($elem->pago_matricula as $hijo) {
                $this->delPagoMatricula($hijo->id); // Borrar hijos
            }
            foreach ($elem->pago_pension as $hijo) {
                $this->delPagoPension($hijo->id); // Borrar hijos
            }
            foreach ($elem->pago_otro as $hijo) {
                $this->delPagoOtros($hijo->id); // Borrar hijos
            }
            foreach ($elem->notas as $hijo) {
                $this->delNota($hijo->id); // Borrar hijos
            }
            foreach ($elem->newasistencia as $hijo) {
                $this->delNewasistencia($hijo->id); // Borrar hijos
            }
            foreach ($elem->matasistencia as $hijo) {
                $this->delMatasistencia($hijo->id); // Borrar hijos
            }
            foreach ($elem->alumnos_has_indicadores as $hijo) {
                $this->delAlumnosHasIndicadores($hijo->id); // Borrar hijos
            }
            $elem->delete(); // Borrar el elemento en si
            return true;
        }
        return false;
    }

    public function delAnios($id){
        $elem=Anios::with('periodos','niveles_has_anios')->findOrFail($id);
        if($this->esUtil($elem)){
            foreach ($elem->periodos as $hijo) {
                $this->delPeriodos($hijo->id); // Borrar hijos
            }
            foreach ($elem->niveles_has_anios as $hijo) {
                $this->delNivelesHasAnios($hijo->id); // Borrar hijos
            }
            $elem->delete(); // Borrar el elemento en si
            return true;
        }
        return false;
    }

    public function delNiveles($id){
        $elem=Niveles::with('niveles_has_anios')->findOrFail($id);
        if($this->esUtil($elem)){
            foreach ($elem->niveles_has_anios as $hijo) {
                $this->delNivelesHasAnios($hijo->id); // Borrar hijos
            }
            $elem->delete(); // Borrar el elemento en si
            return true;
        }
        return false;
    }

    public function delNivelesHasAnios($id){
        $elem=NivelesHasAnios::with('materias_has_niveles','alumnos')->findOrFail($id);
        if($this->esUtil($elem)){
            foreach ($elem->materias_has_niveles as $hijo) {
                $this->delMateriasHasNiveles($hijo->id); // Borrar hijos
            }
            foreach ($elem->alumnos as $hijo) {
                $this->delAlumnos($hijo->id); // Borrar hijos
            }
            $elem->delete(); // Borrar el elemento en si
            return true;
        }
        return false;
    }

    public function delMaterias($id){
        $elem=Materias::with('materias_has_niveles')->findOrFail($id);
        if($this->esUtil($elem)){
            foreach ($elem->materias_has_niveles as $hijo) {
                $this->delMateriasHasNiveles($hijo->id); // Borrar hijos
            }
            $elem->delete(); // Borrar el elemento en si
            return true;
        }
        return false;
    }

    public function delMateriasHasNiveles($id){
        $elem=MateriasHasNiveles::with('materias_has_periodos')->findOrFail($id);
        if($this->esUtil($elem)){
            foreach ($elem->materias_has_periodos as $hijo) {
                $this->delMateriasHasPeriodos($hijo->id); // Borrar hijos
            }
            $elem->delete(); // Borrar el elemento en si
            return true;
        }
        return false;
    }

    public function delPeriodos($id){
        $elem=Periodos::with('materias_has_periodos','newasistencia')->findOrFail($id);
        if($this->esUtil($elem)){
            foreach ($elem->materias_has_periodos as $hijo) {
                $this->delMateriasHasPeriodos($hijo->id); // Borrar hijos
            }
            foreach ($elem->newasistencia as $hijo) {
                $this->delNewasistencia($hijo->id); // Borrar hijos
            }
            $elem->delete(); // Borrar el elemento en si
            return true;
        }
        return false;
    }

    public function delMateriasHasPeriodos($id){
        $elem=MateriasHasPeriodos::with('indicadores','matasistencia')->findOrFail($id);
        if($this->esUtil($elem)){
            foreach ($elem->indicadores as $hijo) {
                $this->delIndicadores($hijo->id); // Borrar hijos
            }
            foreach ($elem->matasistencia as $hijo) {
                $this->delMatasistencia($hijo->id); // Borrar hijos
            }
            $elem->delete(); // Borrar el elemento en si
            return true;
        }
        return false;
    }

    public function delIndicadores($id){
        $elem=Indicadores::with('tipo_nota','alumnos_has_indicadores')->findOrFail($id);
        if($this->esUtil($elem)){
            foreach ($elem->tipo_nota as $hijo) {
                $this->delTipoNota($hijo->id); // Borrar hijos
            }
            foreach ($elem->alumnos_has_indicadores as $hijo) {
                $this->delAlumnosHasIndicadores($hijo->id); // Borrar hijos
            }
            $elem->delete(); // Borrar el elemento en si
            return true;
        }
        return false;
    }

    public function delTipoNota($id){
        $elem=TipoNota::with('notas','indicadores')->findOrFail($id);
        if($this->esUtil($elem)){
            foreach ($elem->notas as $hijo) {
                $this->delNota($hijo->id);
            }
            $elem->delete();
            // Cuando se borre se actualiza el indicador
            $alumind=new AlumInd;
            $alumind->actPromPorIndic($elem->indicadores->id);
            // Borrar hijos
            return true;
        }
        return false;
    }

    public function delNota($id){
        $elem=Notas::with('tipo_nota.indicadores')->findOrFail($id);
        if($this->esUtil($elem)){
            $elem->delete();
            // Borrar hijos
            return true;
        }
        return false;
    }

    public function delAlumnosHasIndicadores($id){
        $elem=AlumnosHasIndicadores::findOrFail($id);
        if($this->esUtil($elem)){
            $elem->delete();
            // Borrar hijos
            return true;
        }
        return false;
    }

    public function delMatasistencia($id){
        $elem=Matasistencia::findOrFail($id);
        if($this->esUtil($elem)){
            $elem->delete();
            // Borrar hijos
            return true;
        }
        return false;
    }

    public function delNewasistencia($id){
        $elem=Newasistencia::findOrFail($id);
        if($this->esUtil($elem)){
            $elem->delete();
            // Borrar hijos
            return true;
        }
        return false;
    }

    public function delEmpleados($id){
        $elem=Empleados::with('pago_salario')-findOrFail($id);
        if($this->esUtil($elem)){
            foreach ($elem->pago_salario as $hijo) {
                $this->delPagoSalario($hijo->id); // Borrar hijos
            }
            $elem->delete();
            // Borrar hijos
            return true;
        }
        return false;
    }

    public function delPagoSalario($id){
        $elem=PagoSalario::findOrFail($id);
        if($this->esUtil($elem)){
            $elem->delete();
            // Borrar hijos
            return true;
        }
        return false;
    }

    public function delPagoOtros($id){
        $elem=PagoOtros::findOrFail($id);
        if($this->esUtil($elem)){
            $elem->delete();
            // Borrar hijos
            return true;
        }
        return false;
    } 

    public function delPagoPension($id){
        $elem=PagoPension::findOrFail($id);
        if($this->esUtil($elem)){
            $elem->delete();
            // Borrar hijos
            return true;
        }
        return false;
    }

    public function delPagoMatricula($id){
        $elem=PagoMatricula::findOrFail($id);
        if($this->esUtil($elem)){
            $elem->delete();
            // Borrar hijos
            return true;
        }
        return false;
    }

    //Funciones generales de la clase

    private function hayAnio($id){
        return $this->esUtil(Anios::findOrFail($id));
    }
    private function hayEventlog($id){
        return $this->esUtil(Eventlog::findOrFail($id));
    }
    private function hayIndicador($id){
        return $this->esUtil(Indicadores::findOrFail($id));
    }
    private function hayTipoNota($id){
        return $this->esUtil(TipoNota::findOrFail($id));
    }
    private function hayMateriasHasNiveles($id){
        return $this->esUtil(MateriasHasNiveles::findOrFail($id));
    }
    private function hayMateriasHasPeriodos($id){
        return $this->esUtil(MateriasHasPeriodos::findOrFail($id));
    }
    private function hayNivelesHasAnios($id){
        return $this->esUtil(NivelesHasAnios::findOrFail($id));
    }
    private function hayPeriodo($id){
        return $this->esUtil(Periodos::findOrFail($id));
    }
    private function hayMateria($id){
        return $this->esUtil(Materias::findOrFail($id));
    }
    private function hayNivel($id){
        return $this->esUtil(Niveles::findOrFail($id));
    }
    private function hayUsuario($id){
        return $this->esUtil(Users::findOrFail($id));
    }
    private function hayAlumno($id){
        return $this->esUtil(Alumnos::findOrFail($id));
    }
    private function hayEmpleado($id){
        return $this->esUtil(Empleados::findOrFail($id));
    }
    private function hayNota($id){
        return $this->esUtil(Notas::findOrFail($id));
    }
    private function esUtil($variable){
        if(!is_object($variable) || is_null($variable) ){
            return false;
        }
        return true;
    }

}
