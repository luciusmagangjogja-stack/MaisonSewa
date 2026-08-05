<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_items', function (Blueprint $table) {
            $table->enum('return_condition', [
                'good',
                'damaged',
                'lost',
                'baik',
                'rusak_ringan',
                'rusak_berat',
                'hilang',
            ])->nullable()->change();
        });

        DB::table('rental_items')
            ->where('return_condition', 'good')
            ->update(['return_condition' => 'baik']);

        DB::table('rental_items')
            ->where('return_condition', 'damaged')
            ->update(['return_condition' => 'rusak_ringan']);

        DB::table('rental_items')
            ->where('return_condition', 'lost')
            ->update(['return_condition' => 'hilang']);

        Schema::table('rental_items', function (Blueprint $table) {
            $table->enum('return_condition', [
                'baik',
                'rusak_ringan',
                'rusak_berat',
                'hilang',
            ])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('rental_items', function (Blueprint $table) {
            $table->enum('return_condition', [
                'baik',
                'rusak_ringan',
                'rusak_berat',
                'hilang',
                'good',
                'damaged',
                'lost',
            ])->nullable()->change();
        });

        DB::table('rental_items')
            ->where('return_condition', 'baik')
            ->update(['return_condition' => 'good']);

        DB::table('rental_items')
            ->where('return_condition', 'rusak_ringan')
            ->update(['return_condition' => 'damaged']);

        DB::table('rental_items')
            ->whereIn('return_condition', ['rusak_berat', 'hilang'])
            ->update(['return_condition' => 'lost']);

        Schema::table('rental_items', function (Blueprint $table) {
            $table->enum('return_condition', [
                'good',
                'damaged',
                'lost',
            ])->nullable()->change();
        });
    }
};
