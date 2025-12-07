<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('membership_tiers', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('description');
            // Will store path like: storage/membership-tiers/gold-tier.png
        });
    }

    public function down()
    {
        Schema::table('membership_tiers', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });
    }
};