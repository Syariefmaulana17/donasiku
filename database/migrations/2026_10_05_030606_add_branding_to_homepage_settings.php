<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('homepage_settings', function (Blueprint $table) {
            $table->string('site_name')->default('DonasiKu');
            $table->string('brand_logo')->nullable(); // Untuk pojok kiri atas
            $table->string('favicon')->nullable(); // Untuk icon tab browser
            
            // 5 Tone Warna
            $table->string('color_primary')->default('#3b82f6');
            $table->string('color_secondary')->default('#10b981');
            $table->string('color_background')->default('#f9fafb');
            $table->string('color_button')->default('#2563eb');
            $table->string('color_accent')->default('#f59e0b');
        });
    }

    public function down()
    {
        Schema::table('homepage_settings', function (Blueprint $table) {
            $table->dropColumn(['site_name', 'brand_logo', 'favicon', 'color_primary', 'color_secondary', 'color_background', 'color_button', 'color_accent']);
        });
    }
};
