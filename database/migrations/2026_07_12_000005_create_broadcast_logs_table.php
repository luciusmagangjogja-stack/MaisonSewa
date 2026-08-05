<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('campaign_id')->constrained('broadcast_campaigns')->cascadeOnDelete();

            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('phone', 20);

            $table->text('rendered_message');

            $table->string('provider');
            $table->string('provider_message_id')->nullable();

            $table->enum('status', ['pending', 'queued', 'sent', 'failed', 'delivered', 'read', 'default pending'])->default('default pending');

            $table->text('error_message')->nullable();
            $table->tinyInteger('attempt_count')->default(0);

            $table->dateTime('sent_at')->nullable();

            $table->timestamps();

            $table->index(['campaign_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_logs');
    }
};

