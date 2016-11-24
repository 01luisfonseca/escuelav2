<?php
namespace App\Helpers\Contracts;

Interface RellenadorContract
{

    public function PeriodosEnMateriasMHP($perId);
    public function MateriasEnPeriodosMHP($matId);

}