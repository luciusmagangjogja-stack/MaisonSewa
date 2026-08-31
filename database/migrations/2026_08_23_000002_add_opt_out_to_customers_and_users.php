<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('opt_out')->default(false)->after('is_blacklisted');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('opt_out')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('opt_out');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('opt_out');
        });
    }
};
