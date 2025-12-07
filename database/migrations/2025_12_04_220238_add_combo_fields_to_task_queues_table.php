<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('task_queues', function (Blueprint $table) {
            $table->foreignId('combo_task_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('is_combo')->default(false);
            
            // Make product_id nullable since combo tasks don't have a single product
            $table->foreignId('product_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('task_queues', function (Blueprint $table) {
            $table->dropForeign(['combo_task_id']);
            $table->dropColumn(['combo_task_id', 'is_combo']);
        });
    }
};