<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->boolean('allow_manual_nominal')->default(true)->after('target_amount');
            $table->integer('min_manual_nominal')->nullable()->after('allow_manual_nominal');
            $table->json('nominal_choices')->nullable()->after('min_manual_nominal');
            $table->string('progress_display_type')->default('terkumpul')->after('nominal_choices');
            $table->string('fb_pixel_id')->nullable()->after('progress_display_type');
            $table->text('fb_access_token')->nullable()->after('fb_pixel_id');
            $table->string('fb_test_event_code')->nullable()->after('fb_access_token');
        });
    }

    public function down()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'allow_manual_nominal', 
                'min_manual_nominal', 
                'nominal_choices', 
                'progress_display_type', 
                'fb_pixel_id', 
                'fb_access_token', 
                'fb_test_event_code'
            ]);
        });
    }
};