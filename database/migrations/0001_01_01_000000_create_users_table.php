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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('numero_legendario')->nullable()->unique();

            $table->string('nombre');
            $table->string('apellido');
            $table->string('rut')->unique();
            $table->date('fecha_nacimiento');
            $table->boolean('enfermedad')->default(false);
            $table->enum('talla', ['S', 'M', 'L', 'XL', 'XXL'])->nullable();

            $table->string('iglesia')->nullable();
            $table->boolean('es_pastor')->default(false);

            $table->string('direccion_calle')->nullable();
            $table->string('direccion_numero')->nullable();
            $table->string('direccion_comuna')->nullable();
            $table->string('direccion_region')->nullable();
            $table->string('direccion_pais')->nullable();
            $table->string('telefono')->nullable();

            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->string('nombre_contacto_emergencia');
            $table->string('parentesco_contacto_emergencia');
            $table->string('telefono_contacto_emergencia');

            $table->enum('estado', ['activo', 'inactivo', 'pendiente'])->default('activo')->index();
            $table->timestamp('fecha_inscripcion')->nullable()->index();

            $table->foreignId('departamento_id')->nullable()->index();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
