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
    Schema::create('web_pages', function (Blueprint $table) {
        $table->id();
        $table->string('title'); // Judul Halaman
        $table->string('slug')->unique(); // Link URL
        $table->longText('content')->nullable(); // Isi Halaman (Bisa menampung HTML sangat panjang)
        $table->boolean('is_active')->default(true); // Status tayang
        $table->timestamps();
    });
    }

    public function down()
    {
    Schema::dropIfExists('web_pages');
    }
};
