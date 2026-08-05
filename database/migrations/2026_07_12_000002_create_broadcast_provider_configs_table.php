<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_provider_configs', function (Blueprint $table) {
            $table->id();
            $table->string('provider_key')->unique();
            $table->string('label');
            $table->boolean('is_active')->default(false);
            $table->boolean('is_default')->default(false);

            $table->text('config')->nullable();
            $table->tinyInteger('priority')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_provider_configs');
    }
};

