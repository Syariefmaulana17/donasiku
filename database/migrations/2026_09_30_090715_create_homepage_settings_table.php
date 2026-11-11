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
        Schema::create('homepage_settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('navbar_color')->default('#ffffff'); // Warna default putih
            $table->json('navbar_menu')->nullable(); // Array untuk link navbar
            $table->json('hero_banners')->nullable(); // Array untuk banner slider
            $table->json('category_buttons')->nullable(); // Array untuk tombol kategori
            $table->json('dynamic_sections')->nullable(); // Array untuk section tanpa batas
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('homepage_settings');
    }
};
