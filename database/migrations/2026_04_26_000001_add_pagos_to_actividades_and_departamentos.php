<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->unsignedBigInteger('precio')->default(0)->after('fecha');
        });

        Schema::table('departamento_actividades', function (Blueprint $table) {
            $table->enum('pago_estado', ['pendiente', 'pagado_parcial', 'pagado'])->default('pendiente')->index()->after('estado');
            $table->unsignedBigInteger('pago_monto_total')->default(0)->after('pago_estado');
            $table->unsignedBigInteger('pago_monto_pagado')->default(0)->after('pago_monto_total');
        });

        Schema::table('finanzas_movimientos', function (Blueprint $table) {
            $table->foreignId('departamento_actividad_id')->nullable()->index()->after('departamento_id');
            $table->foreign('departamento_actividad_id')
                ->references('id')
                ->on('departamento_actividades')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('finanzas_movimientos', function (Blueprint $table) {
            $table->dropForeign(['departamento_actividad_id']);
            $table->dropColumn('departamento_actividad_id');
        });

        Schema::table('departamento_actividades', function (Blueprint $table) {
            $table->dropColumn(['pago_estado', 'pago_monto_total', 'pago_monto_pagado']);
        });

        Schema::table('actividades', function (Blueprint $table) {
            $table->dropColumn('precio');
        });
    }
};
