<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('finanzas_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->constrained('departamentos')->cascadeOnDelete();
            $table->enum('tipo', ['ingreso', 'egreso'])->index();
            $table->unsignedBigInteger('monto');
            $table->string('descripcion');
            $table->date('fecha')->index();
            $table->foreignId('creado_por')->nullable()->index();
            $table->timestamps();

            $table->foreign('creado_por')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finanzas_movimientos');
    }
};
