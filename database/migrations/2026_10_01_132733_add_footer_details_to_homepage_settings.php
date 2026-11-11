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
            $table->string('footer_logo')->nullable();
            $table->text('footer_desc')->nullable();
            $table->string('footer_bg_color')->default('#fff7ed'); // Warna krem Laju Peduli
            $table->json('footer_banks')->nullable(); // Untuk multi-rekening
            $table->string('footer_phone')->nullable();
            $table->string('footer_email')->nullable();
            $table->string('footer_schedule')->nullable();
            $table->text('footer_address')->nullable();
            $table->text('footer_gmaps')->nullable();
        });
    }

    public function down()
    {
        Schema::table('homepage_settings', function (Blueprint $table) {
            $table->dropColumn(['footer_logo', 'footer_desc', 'footer_bg_color', 'footer_banks', 'footer_phone', 'footer_email', 'footer_schedule', 'footer_address', 'footer_gmaps']);
        });
    }
};
