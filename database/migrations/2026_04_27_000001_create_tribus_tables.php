<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tribus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qr_campaign_id')->constrained('qr_campaigns')->cascadeOnDelete();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->foreignId('created_by')->nullable()->index();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['qr_campaign_id', 'nombre']);
        });

        Schema::create('tribu_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tribu_id')->constrained('tribus')->cascadeOnDelete();
            $table->foreignId('senderista_registration_id')->constrained('senderista_registrations')->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->index();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->foreign('assigned_by')->references('id')->on('users')->nullOnDelete();
            $table->unique('senderista_registration_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tribu_members');
        Schema::dropIfExists('tribus');
    }
};
