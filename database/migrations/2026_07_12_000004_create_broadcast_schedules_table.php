<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->foreignId('template_id')->constrained('broadcast_templates');
            $table->string('trigger_event')->nullable();

            $table->enum('frequency', ['once', 'daily', 'weekly', 'monthly', 'yearly']);
            $table->time('run_time');

            $table->tinyInteger('run_day_of_week')->nullable();
            $table->tinyInteger('run_day_of_month')->nullable();
            $table->tinyInteger('run_month')->nullable();

            $table->string('target_type');
            $table->json('target_filters')->nullable();

            $table->string('provider');

            $table->boolean('is_active')->default(true);

            $table->dateTime('next_run_at')->index();
            $table->dateTime('last_run_at')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_schedules');
    }
};

