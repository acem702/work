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
        Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
    $table->string('name')->unique()->nullable();
    $table->integer('phone')->unique()->nullable();
    $table->string('withdrawal_password')->nullable();
    $table->enum('role', ['admin', 'agent', 'user'])->default('user');
    $table->foreignId('membership_tier_id')->constrained()->default(1);
    $table->decimal('point_balance', 15, 2)->default(0);
    $table->foreignId('referrer_id')->nullable()->constrained('users')->nullOnDelete();
    $table->string('referral_code')->unique();
    $table->enum('status', ['active', 'suspended', 'banned'])->default('active');
    $table->timestamp('last_task_date')->nullable();
    $table->integer('tasks_completed_today')->default(0);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
