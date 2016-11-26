<?php

namespace App\Helpers\Contracts;

Interface BorradorContract
{

   public function getLimpiarHuerfanos();
    public function getLimpiarHuerfanosLiviano();
    public function eliminarNivelesHasAniosHuerfanos();
    public function eliminarMateriasHasNivelesHuerfanos();
    public function eliminarMateriasHasPeriodosHuerfanos();
    public function eliminarNewasistenciasHuerfanos();
    public function eliminarMatasistenciasHuerfanos();
    public function eliminarNotasHuerfanos();
    public function eliminarAlumnosHuerfanos();
    public function eliminarEmpleadosHuerfanos();
    public function delUser($id);
    public function delAlumnos($id);
    public function delAnios($id);
    public function delNiveles($id);
    public function delNivelesHasAnios($id);
    public function delMaterias($id);
    public function delMateriasHasNiveles($id);
    public function delPeriodos($id);
    public function delMateriasHasPeriodos($id);
    public function delIndicadores($id);
    public function delTipoNota($id);
    public function delNota($id);
    public function delMatasistencia($id);
    public function delNewasistencia($id);
    public function delEmpleados($id);
    public function delPagoSalario($id);
    public function delPagoOtros($id);
    public function delPagoPension($id);
    public function delPagoMatricula($id);

}