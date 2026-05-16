<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('queued');
            $table->unsignedInteger('message_count')->default(0);
            $table->string('device_id')->nullable();
            $table->string('device_name')->nullable();
            $table->string('device_model')->nullable();
            $table->string('app_version')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_events');
    }
};
