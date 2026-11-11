<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_updates', function (Blueprint $table) {
            $table->id();
            // Menyambungkan berita ini ke campaign mana
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            
            $table->string('title'); // Judul berita
            $table->text('content'); // Isi berita (bisa panjang dan berformat HTML)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_updates');
    }
};