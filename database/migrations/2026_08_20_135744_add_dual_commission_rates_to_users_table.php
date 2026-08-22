<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('commission_rate_serah', 5, 2)->nullable()->after('commission_rate');
            $table->decimal('commission_rate_kembali', 5, 2)->nullable()->after('commission_rate_serah');
        });

        // Backfill: salin commission_rate lama ke kedua kolom baru untuk user sales yang sudah ada
        DB::table('users')
            ->where('role', 'sales')
            ->whereNotNull('commission_rate')
            ->update([
                'commission_rate_serah' => DB::raw('commission_rate'),
                'commission_rate_kembali' => DB::raw('commission_rate'),
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['commission_rate_serah', 'commission_rate_kembali']);
        });
    }
};
