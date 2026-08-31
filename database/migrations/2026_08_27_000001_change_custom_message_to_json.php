<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('broadcast_campaigns')
            ->whereNotNull('custom_message')
            ->where('custom_message', 'NOT LIKE', '[%')
            ->update([
                'custom_message' => DB::raw("JSON_ARRAY(custom_message)"),
            ]);

        Schema::table('broadcast_campaigns', function (Blueprint $table) {
            $table->json('custom_message')->nullable()->after('message_template')->change();
        });
    }

    public function down(): void
    {
        Schema::table('broadcast_campaigns', function (Blueprint $table) {
            $table->text('custom_message')->nullable()->after('message_template')->change();
        });
    }
};
