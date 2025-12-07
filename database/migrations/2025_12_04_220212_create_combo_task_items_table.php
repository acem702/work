<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('combo_task_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combo_task_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('sequence_order');
            $table->timestamps();

            $table->unique(['combo_task_id', 'sequence_order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('combo_task_items');
    }
};