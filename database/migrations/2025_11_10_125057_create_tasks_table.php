<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_id')->constrained()->cascadeOnDelete();
    $table->foreignId('task_queue_id')->nullable()->constrained()->nullOnDelete();
    $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
    $table->decimal('points_locked', 15, 2);
    $table->decimal('commission_earned', 15, 2)->default(0);
    $table->decimal('balance_before', 15, 2);
    $table->decimal('balance_after', 15, 2)->nullable();
    $table->timestamp('submitted_at')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->timestamps();
    
    $table->index(['user_id', 'status']);
    $table->index('status');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
