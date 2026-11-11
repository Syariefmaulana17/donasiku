<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepageSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo', 'navbar_color', 'navbar_menu', 
        'hero_banners', 'category_buttons', 'dynamic_sections',
        'footer_logo', 'footer_desc', 'footer_bg_color', 'footer_banks',
        'footer_phone', 'footer_email', 'footer_schedule', 'footer_address', 'footer_gmaps',
        'custom_pages', 'footer_sections',
        'site_name', 'brand_logo', 'favicon', 'color_primary', 'color_secondary', 'color_background', 'color_button', 'color_accent'
    ];

    protected $casts = [
        'navbar_menu' => 'array',
        'hero_banners' => 'array',
        'category_buttons' => 'array',
        'dynamic_sections' => 'array',
        'custom_pages' => 'array', // <-- Tambahan baru
        'footer_sections' => 'array', // <-- Tambahan baru
        'footer_banks' => 'array',
    ];
}