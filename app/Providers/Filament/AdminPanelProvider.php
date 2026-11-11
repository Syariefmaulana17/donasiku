<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {   
        // 1. Ambil data dengan aman dari pengaturan database
        $setting = null;
        if (app()->runningInConsole() === false) {
            try {
                $setting = \App\Models\HomepageSetting::first();
            } catch (\Exception $e) {}
        }

        // 2. Siapkan variabel Identitas (Fallback otomatis antara brand_logo dan logo header)
        $brandName = $setting->site_name ?? 'DonasiKu';
        $logoPath = !empty($setting->brand_logo) ? $setting->brand_logo : ($setting->logo ?? null);
        $brandLogo = !empty($logoPath) ? '/storage/' . ltrim($logoPath, '/') : null;
        $favicon = !empty($setting->favicon) ? '/storage/' . ltrim($setting->favicon, '/') : null;
        
        // 3. Siapkan Warna (Mengambil dari database)
        $primaryColorHex = $setting->color_primary ?? '#10b981'; // Hijau sebagai default jika kosong
        $bgLoginColor = $setting->color_background ?? '#0f172a'; // Warna gelap sebagai default
        $colorPrimary = \Filament\Support\Colors\Color::hex($primaryColorHex);
        
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName($brandName)
            ->brandLogo($brandLogo)
            ->brandLogoHeight('4rem') // Memperbesar logo
            ->favicon($favicon)
            ->login()
            ->registration() 
            ->passwordReset() 
            ->colors([
                'primary' => $colorPrimary, 
            ])
            
            // 4. INJEKSI CSS PREMIUM UNTUK HALAMAN LOGIN & REGISTER
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => new HtmlString("
                    <style>
                        /* 1. Latar Belakang Gradasi Modern */
                        .fi-simple-layout {
                            background: linear-gradient(135deg, {$bgLoginColor} 0%, {$primaryColorHex} 100%) !important;
                            background-size: 200% 200%;
                            animation: gradientBG 10s ease infinite;
                        }
                        @keyframes gradientBG {
                            0% { background-position: 0% 50%; }
                            50% { background-position: 100% 50%; }
                            100% { background-position: 0% 50%; }
                        }
                        
                        /* 2. Kotak Form (Glassmorphism & Rounded) */
                        .fi-simple-main-ctn > div {
                            border-radius: 2rem !important;
                            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4), 0 0 40px rgba(0,0,0,0.1) !important;
                            padding: 3.5rem 2.5rem !important;
                            border: 1px solid rgba(255, 255, 255, 0.1) !important;
                            background-color: var(--fi-bg) !important; /* Menyesuaikan mode terang/gelap otomatis */
                        }

                        /* 3. Logo Drop Shadow */
                        .fi-logo img {
                            filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.2));
                            margin-bottom: 1rem;
                        }

                        /* 4. Input & Tombol Kekinian */
                        .fi-input-wrapper {
                            border-radius: 1rem !important;
                            transition: all 0.3s ease;
                        }
                        .fi-input-wrapper:focus-within {
                            box-shadow: 0 0 0 2px {$primaryColorHex} !important;
                        }
                        .fi-btn {
                            border-radius: 1rem !important;
                            font-weight: 600 !important;
                            letter-spacing: 0.05em;
                            padding-top: 0.75rem !important;
                            padding-bottom: 0.75rem !important;
                            transition: all 0.3s ease !important;
                        }
                        .fi-btn:hover {
                            transform: translateY(-3px);
                            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
                        }
                    </style>
                ")
            )

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                // Masukkan satpam kita di sini
                \App\Http\Middleware\CheckUserApproval::class,
            ]);
    }
}