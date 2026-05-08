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
        Schema::table('reconocimientos', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->text('descripcion')->nullable()->after('nombre_reconocimiento');
            $table->string('motivo')->default('personalizado')->after('descripcion')->index();
            $table->boolean('enviar_email')->default(false)->after('imagen_path');
            $table->boolean('enviar_whatsapp')->default(false)->after('enviar_email');
            $table->timestamp('email_enviado_at')->nullable()->after('enviar_whatsapp');
            $table->timestamp('whatsapp_preparado_at')->nullable()->after('email_enviado_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reconocimientos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'descripcion',
                'motivo',
                'enviar_email',
                'enviar_whatsapp',
                'email_enviado_at',
                'whatsapp_preparado_at',
            ]);
        });
    }
};
