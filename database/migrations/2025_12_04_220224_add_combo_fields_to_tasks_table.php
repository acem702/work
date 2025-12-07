<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('combo_task_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('combo_sequence')->nullable();
            $table->foreignId('next_combo_task_id')->nullable()->constrained('tasks')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['combo_task_id']);
            $table->dropForeign(['next_combo_task_id']);
            $table->dropColumn(['combo_task_id', 'combo_sequence', 'next_combo_task_id']);
        });
    }
};