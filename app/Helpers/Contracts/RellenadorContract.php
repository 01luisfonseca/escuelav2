<?php
namespace App\Helpers\Contracts;

Interface RellenadorContract
{

	public function autoLlenarAlumno($id);
    public function PeriodosEnMateriasMHP($perId);
    public function MateriasEnPeriodosMHP($matId);
    public function TipoNotaEnNotas($tipoNId);

}