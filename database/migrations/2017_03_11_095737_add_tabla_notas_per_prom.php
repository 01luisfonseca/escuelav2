<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTablaNotasPerProm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alumnos_has_periodos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('alumnos_id');
            $table->integer('materias_has_periodos_id');
            $table->float('prom');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alumnos_has_periodos');
    }
}
