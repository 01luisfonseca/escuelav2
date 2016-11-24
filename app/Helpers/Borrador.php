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
use App\NewAsistencia;
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


class Borrador implements BorradorContract
{

    //Rellena las notas de todos los alumnos nuevos
    // En Desuso por la demora del proceso.
    public function autoLlenarAlumnos(){
        //Primero, buscar los 500 alumnos nuevos de cada nivel
        $alumnos=Alumnos::orderBy('created_at','desc')->take(500)->get();
        // Los comparamos con los indicadores y tipos de nota existentes de cada uno de sus niveles
        // Creamos una colección para los resultados
        $col=collect([]);
        foreach ($alumnos as $alumno) {
            // Buscamos el nivel de cada alumno y sus notas
            $notasNivel=Niveles::where('id',$alumno->niveles_id)
                ->with('materias_has_niveles.niveles_has_periodos.indicadores.tipo_nota.notas')
                ->first();
            foreach ($notasNivel->materias_has_niveles as $materia) {
                // en cada materia
                foreach ($materia->niveles_has_periodos as $periodo) {
                    // En cada periodo
                    foreach ($periodo->indicadores as $indicador) {
                        // En cada indicador
                        foreach ($indicador->tipo_nota as $tipo) {
                            // En cada tipo
                            $encontrado=false;
                            foreach ($tipo->notas as $nota) {
                                // En cada nota, si lo encuentra, pone $encontrado a true, de lo contrario false
                                $encontrado=$nota->alumnos_id==$alumno->id?true:false;
                            }
                            // Si no se encontró al alumno, se crea una nota vacía.
                            if(!$encontrado){
                                $obj=new Notas;
                                $obj->tipo_nota_id=$tipo->id;
                                $obj->nombre_nota='ND';
                                $obj->descripcion='';
                                $obj->calificacion=0;
                                $obj->alumnos_id=$alumno->id;
                                $obj->save();
                                Log::info('Creada nota de alumno ID:'+$alumno->id+'Con el tipo de nota ID:'+$tipo->id);
                                $col->push([
                                    'alumno_id'=>$alumno->id,
                                    'materia_id'=>$materia->id,
                                    'periodo_id'=>$periodo->id,
                                    'indicador_id'=>$indicador->id,
                                    'tipo_nota_id'=>$tipo->id
                                    ]);
                            }
                        }
                    }
                }
            }
        }
        return $col->toJson();  
    }

    //Rellena las notas de un alumno despues de crearlo
    public function autoLlenarAlumno($id){
        $alumno=Alumnos::find($id);
        $notasNivel=Niveles::where('id',$alumno->niveles_id)
            ->with(['materias_has_niveles'=>function($query){
                $query->with(['materias','niveles_has_periodos'=>function($query){
                    $query->with(['periodos','indicadores.tipo_nota.notas']);
                }]);
            }])
            ->first();
        $resultado=[];
        foreach ($notasNivel->materias_has_niveles as $materia) {
            // en cada materia
            foreach ($materia->niveles_has_periodos as $periodo) {
                // En cada periodo
                foreach ($periodo->indicadores as $indicador) {
                    // En cada indicador
                    foreach ($indicador->tipo_nota as $tipo) {
                        // En cada tipo
                        $encontrado=false;
                        foreach ($tipo->notas as $nota) {
                            // En cada nota, si lo encuentra, pone $encontrado a true, de lo contrario false
                            if($nota->alumnos_id==$alumno->id){
                                $encontrado=true;
                                break;
                            }
                        }
                        // Si no se encontró al alumno, se crea una nota vacía.
                        if(!$encontrado){
                            $obj=new Notas;
                            $obj->tipo_nota_id=$tipo->id;
                            $obj->nombre_nota='ND';
                            $obj->descripcion='';
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
        }
        // Devuelve una colección con al menos el mensaje de estado.
        $col=collect([$resultado]);
        return $col->toJson();
    }

    public function getLimpiarHuerfanos(){
        $resultados='Resultados de limpieza de huerfanos: ';
        $resultados.=' '.$this->eliminarAlumnosHuerfanos().' alumnos eliminados, ';
        $resultados.=' '.$this->eliminarEmpleadosHuerfanos().' empleados eliminados, ';
        $resultados.=' '.$this->eliminarMateriasHasNivelesHuerfanos().' niveles-materias eliminadas, ';
        $resultados.=' '.$this->eliminarPeriodosHasNivelesHuerfanos().' niveles-materias-periodos eliminados, ';
        $resultados.=' '.$this->eliminarAsistenciasHuerfanos().' asistencias eliminadas, ';
        $resultados.=' '.$this->eliminarIndicadoresHuerfanos().' indicadores eliminadas, ';
        $resultados.=' '.$this->eliminarTiposHuerfanos().' tipos de nota eliminadas. ';
        //$resultados.=' '.$this->eliminarNotasHuerfanos().' notas eliminadas. ';
        
        return $resultados;
    }

    public function getLimpiarHuerfanosLiviano(){
        $resultados=$this->eliminarAlumnosHuerfanos();
        $resultados+=$this->eliminarEmpleadosHuerfanos();
        $resultados+=$this->eliminarMateriasHasNivelesHuerfanos();
        $resultados+=$this->eliminarPeriodosHasNivelesHuerfanos();
        $resultados+=$this->eliminarAsistenciasHuerfanos();
        $resultados+=$this->eliminarIndicadoresHuerfanos();
        $resultados+=$this->eliminarTiposHuerfanos();
        $resultados+=$this->eliminarNotasHuerfanos();
        return $resultados;
    }

    public function eliminarMateriasHasNivelesHuerfanos(){
        $elementos=MateriasHasNiveles::all();
        $marcado=array();
        $eliminados=0;
        foreach ($elementos as $elemento) {
            if (
                !$this->hayMateria($elemento->materias_id) ||
                !$this->hayNivel($elemento->niveles_id)
                ) 
            {
                $marcado[]=$elemento->id;
            }
            if ($elemento->empleados_id!=0) {
                if(!$this->hayEmpleado($elemento->empleados_id)){
                    $especial=MateriasHasNiveles::find($elemento->id);
                    $especial->empleados_id=0;
                    $especial->save();
                }
            }
        }
        $marcado=array_unique($marcado);
        $eliminados=count($marcado);
        if($eliminados>0){
            foreach ($marcado as $seleccionado) {
                $eliminado=MateriasHasNiveles::find($seleccionado);
                $eliminado->delete();
            }
        }
        return $eliminados;
    }//Verificado

    public function eliminarPeriodosHasNivelesHuerfanos(){
        $elementos=NivelesHasPeriodos::all();
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
                $eliminado=NivelesHasPeriodos::find($seleccionado);
                $eliminado->delete();
            }
        }
        return $eliminados;
    }//Verificado

    public function eliminarAsistenciasHuerfanos(){
        $elementos=Asistencia::all();
        $marcado=array();
        $eliminados=0;
        foreach ($elementos as $elemento) {
            if (
                !$this->hayNivelesHasPeriodos($elemento->niveles_has_periodos_id) ||
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
                $eliminado=Asistencia::find($seleccionado);
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
                !$this->hayNivelesHasPeriodos($elemento->niveles_has_periodos_id)
                ) 
            {
                $marcado[]=$elemento->id;
            }
        }
        $marcado=array_unique($marcado);
        $eliminados=count($marcado);
        if($eliminados>0){
            foreach ($marcado as $seleccionado) {
                $eliminado=Indicadores::find($seleccionado);
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
                $eliminado=TipoNota::find($seleccionado);
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
                $eliminado=Notas::find($seleccionado);
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
                $eliminado=Alumnos::find($seleccionado);
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
                $eliminado=Empleados::find($seleccionado);
                $eliminado->delete();
            }
        }
        return $eliminados;
    }

    //Funciones generales de la clase

    public function hayIndicadores($id){
        return $this->esUtil(Indicadores::find($id));
    }
    public function hayTipoNotas($id){
        return $this->esUtil(TipoNota::find($id));
    }
    public function hayMateriasHasNiveles($id){
        return $this->esUtil(MateriasHasNiveles::find($id));
    }
    public function hayNivelesHasPeriodos($id){
        return $this->esUtil(NivelesHasPeriodos::find($id));
    }
    public function hayPeriodo($id){
        return $this->esUtil(Periodos::find($id));
    }
    public function hayMateria($id){
        return $this->esUtil(Materias::find($id));
    }
    public function hayNivel($id){
        return $this->esUtil(Niveles::find($id));
    }
    public function hayUsuario($id){
        return $this->esUtil(Users::find($id));
    }
    public function hayAlumno($id){
        return $this->esUtil(Alumnos::find($id));
    }
    public function hayEmpleado($id){
        return $this->esUtil(Empleados::find($id));
    }
    public function esUtil($variable){
        if(!is_object($variable) || is_null($variable) ){
            return false;
        }
        return true;
    }

}
