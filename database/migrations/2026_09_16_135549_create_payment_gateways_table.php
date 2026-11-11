<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Nama: midtrans, xendit, doku, dll
            $table->string('public_key')->nullable(); // Untuk Client Key / API Key
            $table->string('secret_key')->nullable(); // Untuk Server Key / Secret Key
            $table->boolean('is_active')->default(false); // Status tombol on/off
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};