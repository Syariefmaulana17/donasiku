<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // 1. Slug (untuk link donasi) & Gambar Cover
            $table->string('cover_image')->nullable()->after('slug');
            
            // 2. Kategori (Zakat, Sedekah, dll) & Tenggat Waktu
            $table->string('category')->nullable()->after('cover_image');
            $table->date('end_date')->nullable()->after('category');
            
            // 3. Lokasi & Google Maps
            $table->string('location')->nullable()->after('target_amount');
            $table->string('gmaps_url')->nullable()->after('location');
            
            // 4. Pengaturan Paket Donasi (Disimpan dalam format JSON)
            $table->boolean('is_package_enabled')->default(false)->after('gmaps_url');
            $table->json('packages')->nullable()->after('is_package_enabled');
            
            // 5. Pengaturan Minimal Donasi
            $table->boolean('is_min_donation_enabled')->default(false)->after('packages');
            $table->unsignedBigInteger('min_donation_amount')->nullable()->after('is_min_donation_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'slug', 'cover_image', 'category', 'end_date', 
                'location', 'gmaps_url', 'is_package_enabled', 
                'packages', 'is_min_donation_enabled', 'min_donation_amount'
            ]);
        });
    }
};