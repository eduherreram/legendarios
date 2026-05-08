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
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();

            $table->foreignId('supervisor_id')->nullable()->index();
            $table->foreignId('lider_id')->nullable()->index();
            $table->foreignId('encargado_id')->nullable()->index();

            $table->timestamps();

            $table->foreign('supervisor_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('lider_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('encargado_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departamentos');
    }
};
