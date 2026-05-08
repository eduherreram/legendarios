<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->enum('pago_estado', ['pendiente', 'abonado', 'pagado', 'freepass'])->default('pendiente')->index()->after('estado');
            $table->unsignedBigInteger('pago_monto_pagado')->default(0)->after('pago_estado');
            $table->timestamp('pago_registrado_en')->nullable()->after('pago_monto_pagado');
            $table->foreignId('pago_registrado_por')->nullable()->index()->after('pago_registrado_en');

            $table->foreign('pago_registrado_por')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->dropForeign(['pago_registrado_por']);
            $table->dropColumn(['pago_estado', 'pago_monto_pagado', 'pago_registrado_en', 'pago_registrado_por']);
        });
    }
};
