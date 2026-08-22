<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->decimal('commission_amount_serah', 12, 2)->nullable()->after('commission_amount');
            $table->enum('commission_status_serah', ['pending', 'earned'])->nullable()->after('commission_amount_serah');
            $table->decimal('commission_amount_kembali', 12, 2)->nullable()->after('commission_status_serah');
            $table->enum('commission_status_kembali', ['pending', 'earned'])->nullable()->after('commission_amount_kembali');
        });

        // Backfill returned_by untuk rental returned yang masih NULL
        // Asumsi: untuk data lama, orang yang buat rental (created_by) juga yang proses pengembalian
        DB::table('rentals')
            ->where('rental_status', 'returned')
            ->whereNull('returned_by')
            ->update(['returned_by' => DB::raw('created_by')]);
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn([
                'commission_amount_serah',
                'commission_status_serah',
                'commission_amount_kembali',
                'commission_status_kembali',
            ]);
        });
    }
};
