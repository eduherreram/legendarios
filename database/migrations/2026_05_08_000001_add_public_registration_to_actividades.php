<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->string('registration_token_hash')->nullable()->unique()->after('precio');
            $table->text('raw_registration_token')->nullable()->after('registration_token_hash');
            $table->timestamp('registration_expires_at')->nullable()->index()->after('raw_registration_token');
        });

        Schema::create('actividad_compromisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actividad_id')->constrained('actividades')->cascadeOnDelete();
            $table->foreignId('departamento_actividad_id')->constrained('departamento_actividades')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('registered_at')->index();
            $table->timestamps();

            $table->unique(['actividad_id', 'user_id']);
        });

        Schema::table('asistencias', function (Blueprint $table) {
            $table->foreignId('actividad_compromiso_id')
                ->nullable()
                ->unique()
                ->after('departamento_actividad_id')
                ->constrained('actividad_compromisos')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->dropForeign(['actividad_compromiso_id']);
            $table->dropColumn('actividad_compromiso_id');
        });

        Schema::dropIfExists('actividad_compromisos');

        Schema::table('actividades', function (Blueprint $table) {
            $table->dropColumn(['registration_token_hash', 'raw_registration_token', 'registration_expires_at']);
        });
    }
};
