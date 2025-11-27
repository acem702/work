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
        Schema::create('transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->enum('type', [
        'task_commission',
        'referral_bonus',
        'admin_topup',
        'membership_upgrade',
        'task_lock',
        'task_refund',
        'withdrawal'
    ]);
    $table->decimal('amount', 15, 2); // Can be negative
    $table->decimal('balance_before', 15, 2);
    $table->decimal('balance_after', 15, 2);
    $table->string('description');
    $table->foreignId('related_task_id')->nullable()->constrained('tasks')->nullOnDelete();
    $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete(); // For admin actions
    $table->timestamps();
    
    $table->index(['user_id', 'created_at']);
    $table->index('type');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
