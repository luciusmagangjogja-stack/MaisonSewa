<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            if (!Schema::hasColumn('rentals', 'fine_status')) {
                $table->enum('fine_status', ['none', 'unpaid', 'partial', 'paid'])
                      ->default('none')
                      ->after('payment_status');
            }
            if (!Schema::hasColumn('rentals', 'fine_amount')) {
                $table->decimal('fine_amount', 12, 2)->default(0)->after('fine_status');
            }
            if (!Schema::hasColumn('rentals', 'fine_paid_amount')) {
                $table->decimal('fine_paid_amount', 12, 2)->default(0)->after('fine_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            if (Schema::hasColumn('rentals', 'fine_paid_amount')) {
                $table->dropColumn('fine_paid_amount');
            }
            if (Schema::hasColumn('rentals', 'fine_amount')) {
                $table->dropColumn('fine_amount');
            }
            if (Schema::hasColumn('rentals', 'fine_status')) {
                $table->dropColumn('fine_status');
            }
        });
    }
};
