<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropIndex(['commission_status', 'created_by']);
            $table->dropColumn([
                'commission_amount',
                'commission_status',
                'commission_amount_serah',
                'commission_status_serah',
                'commission_amount_kembali',
                'commission_status_kembali',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'commission_rate',
                'commission_rate_serah',
                'commission_rate_kembali',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('total_points')->default(0)->after('branch_id');
        });

        Schema::table('rentals', function (Blueprint $table) {
            $table->boolean('points_awarded_serah')->default(false)->nullable()->after('fine_paid_amount');
            $table->boolean('points_awarded_kembali')->default(false)->nullable()->after('points_awarded_serah');
        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn(['points_awarded_serah', 'points_awarded_kembali']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('total_points');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->default(5.00)->after('branch_id');
            $table->decimal('commission_rate_serah', 5, 2)->nullable()->after('commission_rate');
            $table->decimal('commission_rate_kembali', 5, 2)->nullable()->after('commission_rate_serah');
        });

        Schema::table('rentals', function (Blueprint $table) {
            $table->decimal('commission_amount', 12, 2)->nullable()->after('change_amount');
            $table->enum('commission_status', ['pending', 'earned'])->nullable()->after('commission_amount');
            $table->decimal('commission_amount_serah', 12, 2)->nullable()->after('commission_status');
            $table->enum('commission_status_serah', ['pending', 'earned'])->nullable()->after('commission_amount_serah');
            $table->decimal('commission_amount_kembali', 12, 2)->nullable()->after('commission_status_serah');
            $table->enum('commission_status_kembali', ['pending', 'earned'])->nullable()->after('commission_amount_kembali');
            $table->index(['commission_status', 'created_by']);
        });
    }
};
