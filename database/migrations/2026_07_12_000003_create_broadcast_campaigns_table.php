<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->enum('type', ['manual', 'auto']);
            $table->string('trigger_event')->nullable();

            $table->foreignId('template_id')->nullable()->constrained('broadcast_templates')->nullOnDelete();
            $table->text('custom_message')->nullable();

            $table->enum('target_type', ['all', 'branch', 'sales', 'rental_status', 'product', 'category', 'customer']);
            $table->json('target_filters')->nullable();

            $table->string('provider');

            $table->enum('status', ['draft', 'scheduled', 'queued', 'processing', 'completed', 'failed', 'partial', 'default draft'])->default('default draft');

            $table->integer('total_target')->default(0);
            $table->integer('total_success')->default(0);
            $table->integer('total_failed')->default(0);

            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();

            $table->timestamps();

            $table->index(['status']);
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_campaigns');
    }
};

