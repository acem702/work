<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daily_check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('check_in_date');
            $table->integer('streak_day'); // 1, 2, 3... resets to 1 after 7
            $table->decimal('reward_amount', 10, 2);
            $table->timestamps();

            $table->unique(['user_id', 'check_in_date']);
            $table->index(['user_id', 'check_in_date']);
        });

        // Add streak fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->integer('current_streak')->default(0)->after('last_task_date');
            $table->date('last_check_in_date')->nullable()->after('current_streak');
            $table->integer('total_check_ins')->default(0)->after('last_check_in_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_check_ins');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['current_streak', 'last_check_in_date', 'total_check_ins']);
        });
    }
};