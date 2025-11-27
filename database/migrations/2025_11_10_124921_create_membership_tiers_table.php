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
        Schema::create('membership_tiers', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // Bronze, Silver, Gold, etc.
    $table->string('slug')->unique();
    $table->integer('level')->unique(); // 1, 2, 3... (hierarchy)
    $table->integer('daily_task_limit');
    $table->decimal('commission_multiplier', 3, 2); // 1.00, 1.20, 1.50
    $table->decimal('upgrade_cost', 15, 2)->default(0); // Points needed
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_tiers');
    }
};
