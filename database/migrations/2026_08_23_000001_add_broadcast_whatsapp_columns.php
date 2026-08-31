<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('broadcast_templates', function (Blueprint $table) {
            $table->json('variables')->nullable()->after('content');
        });

        Schema::table('broadcast_provider_configs', function (Blueprint $table) {
            $table->string('session_path')->nullable()->after('config');
            $table->dateTime('last_connected_at')->nullable()->after('session_path');
        });

        Schema::table('broadcast_campaigns', function (Blueprint $table) {
            $table->text('message_template')->nullable()->after('custom_message');
            $table->enum('recipient_type', ['customer', 'user', 'both'])->default('customer')->after('target_type');
        });

        Schema::table('broadcast_logs', function (Blueprint $table) {
            $table->enum('recipient_type', ['customer', 'user'])->default('customer')->after('campaign_id');
            $table->unsignedInteger('recipient_id')->nullable()->after('recipient_type');
            $table->index(['recipient_type', 'recipient_id'], 'idx_recipient');
        });
    }

    public function down(): void
    {
        Schema::table('broadcast_logs', function (Blueprint $table) {
            $table->dropIndex('idx_recipient');
            $table->dropColumn(['recipient_id', 'recipient_type']);
        });

        Schema::table('broadcast_campaigns', function (Blueprint $table) {
            $table->dropColumn(['recipient_type', 'message_template']);
        });

        Schema::table('broadcast_provider_configs', function (Blueprint $table) {
            $table->dropColumn(['last_connected_at', 'session_path']);
        });

        Schema::table('broadcast_templates', function (Blueprint $table) {
            $table->dropColumn('variables');
        });
    }
};
