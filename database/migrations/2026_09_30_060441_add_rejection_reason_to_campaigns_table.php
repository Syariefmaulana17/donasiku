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
    Schema::table('campaigns', function (Blueprint $table) {
        // Menambahkan kolom alasan penolakan
        $table->text('rejection_reason')->nullable()->after('status');
    });
    }

    public function down()
    {
    Schema::table('campaigns', function (Blueprint $table) {
        $table->dropColumn('rejection_reason');
    });
    }
};
