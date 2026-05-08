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
        Schema::create('reconocimientos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_reconocimiento');
            $table->string('numero_legendario')->index();
            $table->string('nombre')->nullable();
            $table->string('apellido')->nullable();
            $table->string('imagen_path')->nullable();
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
        Schema::dropIfExists('reconocimientos');
    }
};
