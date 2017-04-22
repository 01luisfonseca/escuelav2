<?php

namespace App\Helpers\Contracts;

Interface AlumIndContract
{

    public function actProm($alumno,$indicador,$reqPers);
    public function actPromPer($alumno,$perId);
    public function addActProm($alumno,$indicador);
    public function actPromPorIndic($indicador);

}