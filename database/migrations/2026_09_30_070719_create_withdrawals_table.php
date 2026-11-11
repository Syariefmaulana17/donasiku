<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Membuat tabel withdrawals lengkap dengan semua kolomnya
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2); // Nominal ditarik
            $table->decimal('platform_fee', 15, 2)->default(0); // Potongan admin
            $table->string('bank_name'); 
            $table->string('account_number'); 
            $table->string('account_name'); 
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('rejection_reason')->nullable(); 
            $table->string('transfer_proof')->nullable(); // Bukti transfer
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('withdrawals');
    }
};