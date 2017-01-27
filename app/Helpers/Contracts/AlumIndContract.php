<?php

namespace App\Helpers\Contracts;

Interface AlumIndContract
{

    public function actProm($alumno,$indicador);
    public function addActProm($alumno,$indicador);
    public function actPromPorIndic($indicador);

}