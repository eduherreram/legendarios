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
        Schema::create('actividades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->date('fecha')->index();
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta')->index();
            $table->timestamp('cerrada_en')->nullable();
            $table->foreignId('cerrada_por')->nullable()->index();
            $table->timestamps();

            $table->foreign('cerrada_por')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('departamento_actividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->constrained('departamentos')->cascadeOnDelete();
            $table->foreignId('actividad_id')->constrained('actividades')->cascadeOnDelete();
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta')->index();
            $table->timestamps();

            $table->unique(['departamento_id', 'actividad_id']);
        });

        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_actividad_id')->constrained('departamento_actividades')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('estado', ['presente', 'ausente'])->default('ausente')->index();
            $table->timestamp('marcada_en')->nullable();
            $table->foreignId('marcada_por')->nullable()->index();
            $table->timestamps();

            $table->unique(['departamento_actividad_id', 'user_id']);
            $table->foreign('marcada_por')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
        Schema::dropIfExists('departamento_actividades');
        Schema::dropIfExists('actividades');
    }
};
