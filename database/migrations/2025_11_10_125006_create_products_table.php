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
        Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->text('category')->nullable();
    $table->decimal('base_points', 15, 2); // Cost to attempt
    $table->decimal('base_commission', 15, 2); // Base earning
    $table->foreignId('min_membership_tier_id')->constrained('membership_tiers');
    $table->string('image_url')->nullable();
    $table->boolean('is_active')->default(true);
    $table->integer('total_submissions')->default(0);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
