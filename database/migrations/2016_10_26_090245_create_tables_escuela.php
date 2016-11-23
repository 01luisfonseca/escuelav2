<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablesEscuela extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pago_otro')) {
        Schema::create('pago_otro', function (Blueprint $table) {
            //Tablas
            $table->increments('id')->unique();
            $table->text('numero_factura');
            $table->integer('alumnos_id');
            $table->float('valor');
            $table->mediumText('descripcion');
            $table->timestamp('cancelado_at');
            $table->timestamps();
            $table->softDeletes();
        });
        }
        if (!Schema::hasTable('tipo_usuario')) {
        Schema::create('tipo_usuario', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('nombre');
            $table->timestamps();
            $table->softDeletes();
        });
        }
        if (!Schema::hasTable('niveles')) {
        Schema::create('niveles', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('nombre');
            $table->mediumText('descripcion');
            $table->timestamps();
            $table->softDeletes();
        });
        }
        if (!Schema::hasTable('materias')) {
        Schema::create('materias', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('nombre');
            $table->mediumText('descripcion');
            $table->timestamps();
            $table->softDeletes();
        });
        }
        if (!Schema::hasTable('periodos')) {
        Schema::create('periodos', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->integer('anios_id');
            $table->string('nombre');
            $table->mediumText('descripcion');
            $table->date('fecha_ini');
            $table->date('fecha_fin');
            $table->timestamps();
            $table->softDeletes();
        });
        }
        Schema::create('newasistencia', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('periodos_id');
            $table->integer('alumnos_id');
            $table->string('name');
            $table->string('lastname');
            $table->integer('authdevice_id');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('anios', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('anio');
            $table->mediumText('descripcion');
            $table->timestamps();
            $table->softDeletes();
        });
        if (!Schema::hasTable('tipo_nota')) {
        Schema::create('tipo_nota', function (Blueprint $table) {
            //Creacion de columnas
            $table->increments('id')->unique();
            $table->string('nombre');
            $table->mediumText('descripcion');
            $table->integer('indicadores_id');
            $table->timestamps();
            $table->softDeletes();
            
        });
        }
        if (!Schema::hasTable('indicadores')) {
        Schema::create('indicadores', function (Blueprint $table) {
            //Creacion de columnas
            $table->increments('id')->unique();
            $table->string('nombre');
            $table->mediumText('descripcion');
            $table->float('porcentaje');
            $table->integer('materias_has_niveles_id');
            $table->integer('periodos_id');
            $table->timestamps();
            $table->softDeletes();
            
        });
        }
        if (!Schema::hasTable('notas')) {
        Schema::create('notas', function (Blueprint $table) {
            //Tablas
            $table->increments('id')->unique();
            $table->integer('tipo_nota_id');
            $table->integer('alumnos_id');
            $table->float('calificacion');
            $table->timestamps();
            $table->softDeletes();
        });
        }
        if (!Schema::hasTable('materias_has_niveles')) {
        Schema::create('materias_has_niveles', function (Blueprint $table) {
            //Tablas
            $table->increments('id')->unique();
            $table->integer('empleados_id');
            $table->integer('niveles_has_anios_id');
            $table->integer('materias_id');
            $table->timestamps();
            $table->softDeletes();
        });
        }
        if (!Schema::hasTable('niveles_has_anios')) {
        Schema::create('niveles_has_anios', function (Blueprint $table) {
            //Tablas
            $table->increments('id')->unique();
            $table->integer('empleados_id');
            $table->integer('anios_id');
            $table->integer('niveles_id');
            $table->timestamps();
            $table->softDeletes();
        });
        }
        if (!Schema::hasTable('alumnos')) {
        Schema::create('alumnos', function (Blueprint $table) {
            //Tablas
            $table->increments('id')->unique();
            $table->integer('users_id');
            $table->integer('niveles_has_anios_id');
            $table->float('pension');
            $table->mediumText('descripcion_pen');
            $table->float('matricula');
            $table->mediumText('descripcion_mat');
            $table->timestamps();
            $table->softDeletes();
        });
        }
        if (!Schema::hasTable('pago_matricula')) {
        Schema::create('pago_matricula', function (Blueprint $table) {
            //Tablas
            $table->increments('id')->unique();
            $table->text('numero_factura');
            $table->integer('alumnos_id');
            $table->float('valor');
            $table->float('faltante');
            $table->mediumText('descripcion');
            $table->timestamp('cancelado_at');
            $table->timestamps();
            $table->softDeletes();
        });
        }
        if (!Schema::hasTable('pago_pension')) {
        Schema::create('pago_pension', function (Blueprint $table) {
            //Tablas
            $table->increments('id')->unique();
            $table->text('numero_factura');
            $table->integer('alumnos_id');
            $table->integer('mes_id');
            $table->float('valor');
            $table->float('faltante');
            $table->mediumText('descripcion');
            $table->timestamp('cancelado_at');
            $table->timestamps();
            $table->softDeletes();
        });
        }
        if (!Schema::hasTable('empleados')) {
        Schema::create('empleados', function (Blueprint $table) {
            //Tablas
            $table->increments('id')->unique();
            $table->integer('users_id');
            $table->integer('salario');
            $table->integer('salario_pagado');
            $table->string('eps');
            $table->float('eps_val');
            $table->string('arl');
            $table->float('arl_val');
            $table->string('pension');          
            $table->float('pension_val');
            $table->date('contrato_ini');
            $table->date('contrato_fin');
            $table->timestamps();
            $table->softDeletes();
        });
        }
        if (!Schema::hasTable('pago_salario')) {
        Schema::create('pago_salario', function (Blueprint $table) {
            //Tablas
            $table->increments('id')->unique();
            $table->date('fecha_ini');
            $table->date('fecha_fin');
            $table->integer('anio');
            $table->integer('mes_id');
            $table->smallInteger('dias');
            $table->integer('empleados_id');
            $table->float('salario_pagado');
            $table->float('auxmovil');
            $table->float('eps_empleado');
            $table->float('eps_empresa');
            $table->float('pension_empleado');
            $table->float('pension_empresa');
            $table->float('arl_empresa');
            $table->float('descuento');
            $table->mediumText('descripcion_desc');
            $table->float('bonificacion');
            $table->mediumText('descripcion_boni');
            $table->mediumText('notas');
            $table->timestamp('pagado_at');
            $table->timestamps();
            $table->softDeletes();
        });
        }
        if (!Schema::hasTable('mes')) {
        Schema::create('mes', function (Blueprint $table) {
            //Tablas
            $table->increments('id')->unique();
            $table->text('nombre');
            $table->timestamps();
            $table->softDeletes();
        });
        }
        Schema::create('authdevice', function (Blueprint $table) {
            $table->increments('id');
            $table->string('serial');
            $table->string('nombre');
            $table->mediumText('descripcion');
            $table->boolean('estado');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('generales', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('nombre');
            $table->string('valor',100);
            $table->mediumText('descripcion');
            $table->softDeletes();
        });
        Schema::create('asiserved', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('tarjeta');
            $table->string('lectora');
            $table->softDeletes();
        });
        Schema::create('tarjetas', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('tarjeta');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pago_otro');
        Schema::dropIfExists('tipo_usuario');
        Schema::dropIfExists('niveles');
        Schema::dropIfExists('materias');
        Schema::dropIfExists('periodos');
        Schema::dropIfExists('newasistencia');
        Schema::dropIfExists('notas');
        Schema::dropIfExists('tipo_nota');
        Schema::dropIfExists('indicadores');
        Schema::dropIfExists('materias_has_niveles');
        Schema::dropIfExists('niveles_has_anios');
        Schema::dropIfExists('alumnos');
        Schema::dropIfExists('pago_matricula');
        Schema::dropIfExists('pago_pension');
        Schema::dropIfExists('empleados');
        Schema::dropIfExists('pago_salario');
        Schema::dropIfExists('mes');
        Schema::dropIfExists('authdevice');
        Schema::dropIfExists('generales');
        Schema::dropIfExists('asiserved');
        Schema::dropIfExists('tarjetas');
    }
}
