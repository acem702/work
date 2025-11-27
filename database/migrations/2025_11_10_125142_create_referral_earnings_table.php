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
        Schema::create('referral_earnings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('referee_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('task_id')->nullable()->constrained()->nullOnDelete();
    $table->decimal('amount', 15, 2);
    $table->integer('referral_level')->default(1); // 1st level, 2nd level
    $table->enum('earning_type', ['task_commission', 'membership_upgrade']);
    $table->timestamps();
    
    $table->index(['referrer_id', 'created_at']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_earnings');
    }
};
