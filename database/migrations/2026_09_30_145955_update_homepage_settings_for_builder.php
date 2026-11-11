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
        $table->json('custom_pages')->nullable()->after('dynamic_sections');
        $table->json('footer_sections')->nullable()->after('custom_pages');
    });
    }

    public function down()
    {
    Schema::table('homepage_settings', function (Blueprint $table) {
        $table->dropColumn(['custom_pages', 'footer_sections']);
    });
    }
};
