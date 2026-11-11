@php
    $homeSetting = \App\Models\HomepageSetting::first();
    $siteName = $homeSetting->site_name ?? 'DonasiKu';
    $favicon = !empty($homeSetting->favicon) ? Storage::url($homeSetting->favicon) : null;
    $colorPrimary = $homeSetting->color_primary ?? '#2563eb';
    $colorSecondary = $homeSetting->color_secondary ?? '#10b981';
    $colorBg = $homeSetting->color_background ?? '#f8fafc';
    $colorBtn = $homeSetting->color_button ?? '#2563eb';
    $colorAccent = $homeSetting->color_accent ?? '#f59e0b';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>{{ $title ?? $siteName . ' - Platform Kebaikan Bersama' }}</title>

        @if($favicon)
            <link rel="icon" href="{{ $favicon }}">
        @endif

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        },
                        colors: {
                            brand: {
                                primary: '{{ $colorPrimary }}',
                                secondary: '{{ $colorSecondary }}',
                                btn: '{{ $colorBtn }}',
                                accent: '{{ $colorAccent }}',
                            }
                        }
                    }
                }
            }
        </script>

        <style>
            :root {
                --color-primary: {{ $colorPrimary }};
                --color-secondary: {{ $colorSecondary }};
                --color-bg: {{ $colorBg }};
                --color-btn: {{ $colorBtn }};
                --color-accent: {{ $colorAccent }};
            }
            .hide-scroll-bar { -ms-overflow-style: none; scrollbar-width: none; }
            .hide-scroll-bar::-webkit-scrollbar { display: none; }
            .prose h2 { font-size: 1.35rem; font-weight: 700; margin-top: 1.25rem; margin-bottom: 0.75rem; color: #0f172a; }
            .prose h3 { font-size: 1.15rem; font-weight: 700; margin-top: 1rem; margin-bottom: 0.5rem; color: #1e293b; }
            .prose p { margin-bottom: 0.9rem; line-height: 1.75; }
            .prose ul { list-style-type: disc; margin-left: 1.25rem; margin-bottom: 0.9rem; }
            .prose ol { list-style-type: decimal; margin-left: 1.25rem; margin-bottom: 0.9rem; }
            .prose a { color: var(--color-primary); text-decoration: underline; }
            .prose img { border-radius: 0.75rem; margin: 1rem 0; max-width: 100%; height: auto; }
        </style>

        @livewireStyles
    </head>
    <body class="antialiased font-sans text-slate-800 bg-slate-50">
        
        {{ $slot }}

        @livewireScripts
    </body>
</html>
