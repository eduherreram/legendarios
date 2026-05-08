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
        Schema::create('qr_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('ends_at')->nullable()->index();
            $table->boolean('activa')->default(true)->index();
            $table->foreignId('creado_por')->nullable()->index();
            $table->timestamps();

            $table->foreign('creado_por')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('qr_registration_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qr_campaign_id')->nullable()->constrained('qr_campaigns')->nullOnDelete();

            $table->string('token_hash')->unique();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('used_at')->nullable()->index();
            $table->foreignId('used_by_user_id')->nullable()->index();

            $table->foreignId('created_by')->nullable()->index();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('used_by_user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('senderista_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('qr_registration_token_id')->nullable()->constrained('qr_registration_tokens')->nullOnDelete();
            $table->timestamp('registered_at')->index();
            $table->timestamps();

            $table->unique(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('senderista_registrations');
        Schema::dropIfExists('qr_registration_tokens');
        Schema::dropIfExists('qr_campaigns');
    }
};
