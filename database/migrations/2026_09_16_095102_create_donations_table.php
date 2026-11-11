<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            
            // --- KODE LACI KASIR KITA ---
            // Relasi: Donasi ini untuk kampanye yang mana?
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            
            // Data Donatur & Transaksi
            $table->string('donor_name');
            $table->string('donor_email')->nullable();
            $table->decimal('amount', 15, 2);
            
            // Status pembayaran (pending / success / failed)
            $table->string('status')->default('pending');
            // ----------------------------

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};