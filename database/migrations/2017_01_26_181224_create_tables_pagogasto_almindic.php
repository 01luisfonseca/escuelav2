<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablesPagogastoAlmindic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alumnos_has_indicadores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('alumnos_id');
            $table->integer('indicadores_id');
            $table->float('prom');
            $table->timestamps();
        });
        Schema::create('pago_gasto', function (Blueprint $table) {
            $table->increments('id');
            $table->string('numero_factura');
            $table->float('valor');
            $table->mediumText('descripcion')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('alumnos_has_indicadores');
        Schema::dropIfExists('pago_gasto');
    }
}
