<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->decimal('commission_amount', 12, 2)->nullable()->after('change_amount');
            $table->enum('commission_status', ['pending', 'earned'])->nullable()->after('commission_amount');
            $table->index(['commission_status', 'created_by']);
        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropIndex(['commission_status', 'created_by']);
            $table->dropColumn(['commission_amount', 'commission_status']);
        });
    }
};
