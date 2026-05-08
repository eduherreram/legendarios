<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('marketing_campaign_logs');
        Schema::dropIfExists('marketing_campaigns');

        if (Schema::hasTable('roles')) {
            Role::query()->where('name', 'Marketing')->delete();
        }
    }

    public function down(): void
    {
        //
    }
};
