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

    /**
     * Normalisasi URL menu navbar agar selalu valid dari halaman mana pun
     * dan tidak pernah terjebak menjadi relative URL seperti /halaman/donasi.dqws.org.
     */
    public static function normalizeNavUrl(?string $url, ?string $label = null): string
    {
        $raw = trim((string) $url);
        $cleanLabel = strtolower(trim((string) $label));

        if ($raw === '' || $raw === '/' || in_array(strtolower($raw), ['beranda', 'home', '/beranda', '/home'], true)) {
            return '/';
        }

        // Bersihkan jika sempat tersimpan dengan prefix /halaman/ diikuti domain
        $raw = preg_replace('#^/?halaman/(https?://|donasi\.dqws\.org|www\.donasi\.dqws\.org)#i', '$1', $raw) ?? $raw;

        $host = request()->getHost() ?: 'donasi.dqws.org';
        $hostPattern = '#^(?:https?://)?(?:www\.)?(?:' . preg_quote($host, '#') . '|donasi\.dqws\.org|localhost(?::\d+)?|127\.0\.0\.1(?::\d+)?)(/.*)?$#i';

        if (preg_match($hostPattern, $raw, $matches)) {
            $path = $matches[1] ?? '/';
            return ($path === '' || $path === '/') ? '/' : $path;
        }

        if (in_array($cleanLabel, ['beranda', 'home', 'halaman utama'], true) && ! str_starts_with($raw, '/')) {
            return '/';
        }

        if (str_starts_with($raw, '#')) {
            return '/' . $raw;
        }

        if (preg_match('#^(https?://|mailto:|tel:|wa\.me/)#i', $raw)) {
            return str_starts_with(strtolower($raw), 'wa.me/') ? 'https://' . $raw : $raw;
        }

        return '/' . ltrim($raw, '/');
    }
}