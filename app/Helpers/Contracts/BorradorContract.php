<?php

namespace App\Helpers\Contracts;

Interface BorradorContract
{

    public function limpiarAnio($id);
    public function limpiarPeriodo($id);
    public function limpiarMateria($id);
    public function limpiarNivel($id);
    public function limpiarNivelesHasAnio($id);
    public function limpiarMateriasHasNivel($id);
    public function limpiarIndicador($id);
    public function limpiarTipoNota($id);
    public function limpiarNota($id);
    public function limpiarAsistencia($id);

}