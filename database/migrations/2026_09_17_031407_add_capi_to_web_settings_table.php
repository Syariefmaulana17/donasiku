<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_settings', function (Blueprint $table) {
            $table->text('capi_token')->nullable()->after('pixel_id');
            $table->string('test_event_code')->nullable()->after('capi_token'); // Berguna untuk testing CAPI nanti
        });
    }

    public function down(): void
    {
        Schema::table('web_settings', function (Blueprint $table) {
            $table->dropColumn(['capi_token', 'test_event_code']);
        });
    }
};