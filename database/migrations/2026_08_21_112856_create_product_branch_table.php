<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_branch', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['product_id', 'branch_id']);
        });

        // Backfill: untuk setiap produk yang sudah ada (punya branch_id),
        // buat relasi di product_branch agar tidak kehilangan cabang setelah migrasi
        DB::table('products')->whereNotNull('branch_id')->get()->each(function ($product) {
            DB::table('product_branch')->insert([
                'product_id' => $product->id,
                'branch_id' => $product->branch_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_branch');
    }
};
