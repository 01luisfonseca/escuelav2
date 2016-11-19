<?php

namespace App\Helpers;

use App\Helpers\Contracts\BorradorContract;
use Illuminate\Http\Request;
use App\Anios;
use App\Periodos;
use App\Materias;
use App\Niveles;
use App\NivelesHasAnios;
use App\MateriasHasNiveles;
use App\Indicadores;
use App\TipoNota;
use App\Nota;
use App\NewAsistencia;


class Borrador implements BorradorContract
{

    public function limpiarAnio($id){}
    public function limpiarPeriodo($id){}
    public function limpiarMateria($id){}
    public function limpiarNivel($id){}
    public function limpiarNivelesHasAnio($id){}
    public function limpiarMateriasHasNivel($id){}
    public function limpiarIndicador($id){}
    public function limpiarTipoNota($id){}
    public function limpiarNota($id){}
    public function limpiarAsistencia($id){}

}
