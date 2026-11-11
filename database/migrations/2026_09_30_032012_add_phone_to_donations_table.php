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
    Schema::table('donations', function (Blueprint $table) {
        // Menambahkan kolom phone setelah kolom donor_name
        $table->string('phone')->nullable()->after('donor_name'); 
    });
    }

    public function down()
    {
    Schema::table('donations', function (Blueprint $table) {
        $table->dropColumn('phone');
    });
    }
};
