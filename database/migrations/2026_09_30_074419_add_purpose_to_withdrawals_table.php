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
    Schema::table('withdrawals', function (Blueprint $table) {
        $table->text('purpose')->nullable()->after('amount'); // Kolom tujuan pencairan
    });
    }

    public function down()
    {
    Schema::table('withdrawals', function (Blueprint $table) {
        $table->dropColumn('purpose');
    });
    }
};
