<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add id_photo to guarantees table
        if (Schema::hasTable('guarantees') && !Schema::hasColumn('guarantees', 'id_photo')) {
            Schema::table('guarantees', function (Blueprint $table) {
                $table->string('id_photo', 255)->nullable()->after('description');
            });
        }

        // Add payment_method to rentals table
        if (Schema::hasTable('rentals') && !Schema::hasColumn('rentals', 'payment_method')) {
            Schema::table('rentals', function (Blueprint $table) {
                $table->string('payment_method', 20)->nullable()->after('payment_status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('guarantees') && Schema::hasColumn('guarantees', 'id_photo')) {
            Schema::table('guarantees', function (Blueprint $table) {
                $table->dropColumn('id_photo');
            });
        }

        if (Schema::hasTable('rentals') && Schema::hasColumn('rentals', 'payment_method')) {
            Schema::table('rentals', function (Blueprint $table) {
                $table->dropColumn('payment_method');
            });
        }
    }
};

