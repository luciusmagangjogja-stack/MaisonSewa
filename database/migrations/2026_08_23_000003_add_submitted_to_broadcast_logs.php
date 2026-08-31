<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('broadcast_logs', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'queued',
                'submitted',
                'sent',
                'delivered',
                'read',
                'failed',
                'default pending',
            ])->default('default pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('broadcast_logs', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'queued',
                'sent',
                'failed',
                'delivered',
                'read',
                'default pending',
            ])->default('default pending')->change();
        });
    }
};
