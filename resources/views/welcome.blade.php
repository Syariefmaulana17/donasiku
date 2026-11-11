@php
    $homeSetting = \App\Models\HomepageSetting::first();
    $siteName = $homeSetting->site_name ?? 'DonasiKu';
    $favicon = !empty($homeSetting->favicon) ? Storage::url($homeSetting->favicon) : null;
    $navbarColor = $homeSetting->navbar_color ?? '#ffffff';
    $colorPrimary = $homeSetting->color_primary ?? '#2563eb';
    $colorSecondary = $homeSetting->color_secondary ?? '#10b981';
    $colorBg = $homeSetting->color_background ?? '#f8fafc';
    $colorBtn = $homeSetting->color_button ?? '#2563eb';
    $colorAccent = $homeSetting->color_accent ?? '#f59e0b';

    // Statistik Dampak Real-Time
    $totalTerkumpul = \App\Models\Donation::where('status', 'success')->sum('amount');
    $totalProgramAktif = \App\Models\Campaign::where('status', 'active')->count();
    $totalOrangBaik = \App\Models\Donation::where('status', 'success')->count();
    $latestArticles = \App\Models\Article::with('category')->where('is_published', true)->latest()->take(3)->get();
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteName }} — Platform Kolaborasi Kebaikan</title>
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
        .prose h2 { font-size: 1.5rem; font-weight: 800; margin-top: 1.5rem; margin-bottom: 0.75rem; color: #0f172a; }
        .prose p { margin-bottom: 1rem; line-height: 1.75; color: #334155; }
        .prose a { color: var(--color-primary); text-decoration: underline; }
    </style>

    @livewireStyles
</head>
<body class="bg-slate-50 text-slate-800 font-sans flex flex-col min-h-screen antialiased">
    
    <!-- HEADER / STICKY NAVBAR -->
    <nav x-data="{ open: false }" class="sticky top-0 z-50 border-b border-slate-200/80 backdrop-blur-md transition-all" style="background-color: {{ $navbarColor }}f2">
        <div class="container mx-auto px-4 md:px-8 h-16 md:h-20 flex justify-between items-center max-w-7xl">
            <a href="/" class="flex items-center gap-2.5 group">
                @if($homeSetting && $homeSetting->logo)
                    <img src="{{ Storage::url($homeSetting->logo) }}" alt="{{ $siteName }}" class="h-9 md:h-11 object-contain">
                @elseif($homeSetting && $homeSetting->brand_logo)
                    <img src="{{ Storage::url($homeSetting->brand_logo) }}" alt="{{ $siteName }}" class="h-9 md:h-11 object-contain">
                @else
                    <div class="w-10 h-10 rounded-xl bg-brand-primary text-white flex items-center justify-center font-extrabold text-lg shadow-md shadow-blue-500/20">
                        {{ substr($siteName, 0, 1) }}
                    </div>
                    <span class="text-xl md:text-2xl font-extrabold text-slate-900 tracking-tight">
                        {{ $siteName }}<span class="text-brand-primary">.</span>
                    </span>
                @endif
            </a>

            <!-- Menu Desktop -->
            <div class="hidden md:flex items-center gap-3">
                @if($homeSetting && !empty($homeSetting->navbar_menu))
                    @foreach($homeSetting->navbar_menu as $menu)
                        <a href="{{ $menu['url'] }}" class="px-4 py-2 rounded-xl text-slate-700 hover:text-brand-primary hover:bg-slate-100/80 font-semibold transition text-sm">
                            {{ $menu['label'] }}
                        </a>
                    @endforeach
                @else
                    <a href="#program-kebaikan" class="px-4 py-2 rounded-xl text-slate-700 hover:text-brand-primary hover:bg-slate-100/80 font-semibold transition text-sm">Program</a>
                    @if($latestArticles->count() > 0)
                        <a href="#kabar-kebaikan" class="px-4 py-2 rounded-xl text-slate-700 hover:text-brand-primary hover:bg-slate-100/80 font-semibold transition text-sm">Artikel</a>
                    @endif
                @endif

                <div class="h-5 w-px bg-slate-200 mx-1"></div>

                @auth
                    <a href="/admin" class="bg-brand-btn text-white px-5 py-2.5 rounded-xl shadow-sm hover:opacity-95 font-bold transition text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Ruang Kendali
                    </a>
                @else
                    <a href="#program-kebaikan" class="bg-brand-btn text-white px-5 py-2.5 rounded-xl shadow-sm hover:opacity-95 font-bold transition text-sm">
                        Donasi Sekarang
                    </a>
                @endauth
            </div>

            <!-- Hamburger Mobile -->
            <div class="md:hidden flex items-center gap-2">
                <a href="#program-kebaikan" class="bg-brand-btn text-white px-3.5 py-2 rounded-xl font-bold text-xs shadow-sm">
                    Donasi
                </a>
                <button @click="open = !open" class="text-slate-700 focus:outline-none p-2 bg-slate-100 rounded-xl">
                    <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    <svg x-show="open" style="display: none;" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Dropdown Menu Mobile -->
        <div x-show="open" style="display: none;" x-transition class="md:hidden bg-white border-t border-slate-100 shadow-xl px-4 py-4 space-y-2">
            @if($homeSetting && !empty($homeSetting->navbar_menu))
                @foreach($homeSetting->navbar_menu as $menu)
                    <a href="{{ $menu['url'] }}" class="block px-4 py-3 rounded-xl text-slate-700 hover:bg-slate-50 font-bold text-sm">{{ $menu['label'] }}</a>
                @endforeach
            @else
                <a href="#program-kebaikan" @click="open = false" class="block px-4 py-3 rounded-xl text-slate-700 hover:bg-slate-50 font-bold text-sm">Program Kebaikan</a>
            @endif
            @auth
                <a href="/admin" class="block text-center bg-brand-btn text-white px-4 py-3 rounded-xl font-bold text-sm">Ruang Kendali</a>
            @endauth
        </div>
    </nav>

    <!-- CONTENT / HALAMAN UTAMA -->
    <main class="flex-grow pb-12">
        @if($homeSetting && !empty($homeSetting->dynamic_sections))
            @foreach($homeSetting->dynamic_sections as $block)
                
                {{-- 1. BLOK BANNER SLIDER --}}
                @if($block['type'] === 'banner_slider')
                    @if(!empty($block['data']['slides']))
                        <div x-data="{
                                scrollNext() { $refs.bannerSlider.scrollBy({left: $refs.bannerSlider.clientWidth, behavior: 'smooth'}) },
                                scrollPrev() { $refs.bannerSlider.scrollBy({left: -$refs.bannerSlider.clientWidth, behavior: 'smooth'}) }
                            }" class="relative px-4 md:px-8 mt-5 md:mt-8 max-w-7xl mx-auto group">
                            
                            <button @click="scrollPrev()" class="hidden md:flex absolute left-12 top-1/2 -translate-y-1/2 z-10 bg-white/95 hover:bg-white text-slate-800 hover:text-brand-primary rounded-full p-3 shadow-lg transition-all opacity-0 group-hover:opacity-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                            </button>

                            <div x-ref="bannerSlider" class="flex overflow-x-auto snap-x snap-mandatory hide-scroll-bar gap-4 md:gap-6 rounded-2xl md:rounded-3xl">
                                @foreach($block['data']['slides'] as $slide)
                                <div class="shrink-0 w-full snap-center relative rounded-2xl md:rounded-3xl overflow-hidden shadow-md h-52 sm:h-72 md:h-[420px] bg-slate-200">
                                    <a href="{{ $slide['link_url'] ?? '#program-kebaikan' }}" class="block w-full h-full">
                                        <img src="{{ Storage::url($slide['image']) }}" class="w-full h-full object-cover" alt="Banner Slide">
                                    </a>
                                </div>
                                @endforeach
                            </div>

                            <button @click="scrollNext()" class="hidden md:flex absolute right-12 top-1/2 -translate-y-1/2 z-10 bg-white/95 hover:bg-white text-slate-800 hover:text-brand-primary rounded-full p-3 shadow-lg transition-all opacity-0 group-hover:opacity-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                    @endif

                {{-- 2. BLOK TOMBOL KATEGORI --}}
                @elseif($block['type'] === 'category_buttons')
                    @if(!empty($block['data']['buttons']))
                        <div class="px-4 md:px-8 mt-6 md:mt-8 max-w-7xl mx-auto">
                            <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm p-5 md:p-7 border border-slate-100">
                                <div class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-6 lg:flex lg:justify-around gap-4 md:gap-6 text-center">
                                    @foreach($block['data']['buttons'] as $cat)
                                    <a href="{{ $cat['link'] ?? '#program-kebaikan' }}" class="flex flex-col items-center group cursor-pointer">
                                        <div class="w-13 h-13 md:w-16 md:h-16 bg-blue-50/80 group-hover:bg-brand-primary text-brand-primary group-hover:text-white rounded-2xl flex items-center justify-center mb-2.5 shadow-sm group-hover:shadow-md group-hover:-translate-y-1 transition-all duration-200 p-3">
                                            @if(!empty($cat['icon_svg']))
                                                {!! $cat['icon_svg'] !!}
                                            @else
                                                <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                            @endif
                                        </div>
                                        <span class="text-xs md:text-sm font-bold text-slate-700 group-hover:text-brand-primary transition leading-tight">{{ $cat['title'] ?? '' }}</span>
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                
                {{-- 3. BLOK BANNER STATIS (1 Gambar) --}}
                @elseif($block['type'] === 'banner_statis')
                    <div class="max-w-7xl mx-auto px-4 md:px-8 mt-8 md:mt-10">
                        <a href="{{ $block['data']['link_url'] ?? '#' }}" class="block rounded-2xl md:rounded-3xl overflow-hidden shadow-sm hover:shadow-lg transition">
                            <img src="{{ Storage::url($block['data']['image']) }}" class="w-full h-auto object-cover" alt="Banner Statis">
                        </a>
                    </div>
                
                {{-- 4. BLOK DAFTAR CAMPAIGN --}}
                @elseif($block['type'] === 'campaign_list')
                    <div id="program-kebaikan">
                        <livewire:campaign-search 
                            :title="$block['data']['title']" 
                            :subtitle="$block['data']['subtitle'] ?? ''" 
                            :category="$block['data']['category_filter'] ?? 'semua'" 
                            wire:key="block-{{ $loop->index }}" 
                        />
                    </div>
                
                {{-- 5. BLOK DAFTAR ARTIKEL (Live Data dari Tabel Articles) --}}
                @elseif($block['type'] === 'article_list')
                    <section id="kabar-kebaikan" class="max-w-7xl mx-auto px-4 md:px-8 mt-12 md:mt-16">
                        <div class="flex flex-col md:flex-row md:items-end justify-between mb-6 gap-2">
                            <div>
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-bold mb-2">
                                    Inspirasi & Laporan
                                </div>
                                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $block['data']['title'] ?? 'Kabar & Inspirasi Kebaikan' }}</h2>
                                @if(!empty($block['data']['subtitle']))
                                    <p class="text-sm md:text-base text-slate-500 mt-1">{{ $block['data']['subtitle'] }}</p>
                                @endif
                            </div>
                        </div>

                        @if($latestArticles->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @foreach($latestArticles as $article)
                                    <a href="{{ route('article.detail', $article->slug) }}" class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col group">
                                        <div class="relative h-48 bg-slate-100 overflow-hidden">
                                            @if($article->cover_image)
                                                <img src="{{ Storage::url($article->cover_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-400 text-xs">Tanpa Gambar</div>
                                            @endif
                                            @if($article->category)
                                                <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg bg-white/95 backdrop-blur-sm text-brand-primary font-bold text-[11px] shadow-sm">
                                                    {{ $article->category->name }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="p-5 flex flex-col flex-grow">
                                            <span class="text-xs text-slate-400 font-medium mb-1.5">{{ $article->created_at->translatedFormat('d F Y') }}</span>
                                            <h3 class="font-bold text-slate-900 text-base line-clamp-2 group-hover:text-brand-primary transition-colors mb-2">
                                                {{ $article->title }}
                                            </h3>
                                            <p class="text-xs text-slate-500 line-clamp-3 mb-4">
                                                {{ \Illuminate\Support\Str::limit(strip_tags($article->content), 120) }}
                                            </p>
                                            <span class="mt-auto text-xs font-bold text-brand-primary inline-flex items-center gap-1 group-hover:gap-2 transition-all">
                                                Baca Selengkapnya
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="p-8 bg-white border border-dashed border-slate-200 rounded-2xl text-center text-slate-400 text-sm">
                                Belum ada artikel yang dipublikasikan.
                            </div>
                        @endif
                    </section>

                {{-- 6. BLOK TEKS HTML --}}
                @elseif($block['type'] === 'html_content')
                    <div class="max-w-7xl mx-auto px-4 md:px-8 mt-10">
                        <div class="bg-white rounded-2xl p-6 md:p-8 border border-slate-100 shadow-sm prose max-w-none">
                            {!! $block['data']['content'] !!}
                        </div>
                    </div>
                @endif

            @endforeach
        @else
            <!-- DEFAULT FALLBACK JIKA PAGE BUILDER BELUM DIISI -->
            <div id="program-kebaikan">
                <livewire:campaign-search title="Program Kebaikan Pilihan" subtitle="Salurkan donasi terbaik Anda untuk membantu sesama." category="semua" />
            </div>
        @endif
    </main>

    <!-- FOOTER PROFESIONAL -->
    <footer class="border-t border-slate-200/80 py-12 md:py-16 mt-8" style="background-color: {{ $homeSetting->footer_bg_color ?? '#fff7ed' }}">
        <div class="container mx-auto px-4 md:px-8 max-w-7xl">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-10">

                <!-- Kolom 1: Identitas & Deskripsi -->
                <div>
                    @if($homeSetting && $homeSetting->footer_logo)
                        <img src="{{ Storage::url($homeSetting->footer_logo) }}" alt="Logo Footer" class="h-14 object-contain mb-4">
                    @else
                        <h3 class="text-2xl font-extrabold text-brand-primary mb-4">{{ $siteName }}</h3>
                    @endif
                    <p class="text-slate-600 text-sm leading-relaxed">
                        {{ $homeSetting->footer_desc ?? 'Mari bersinergi dalam aksi nyata untuk kebaikan bersama. Langkah kecil kepedulian Anda adalah harapan baru bagi mereka.' }}
                    </p>
                </div>

                <!-- Kolom 2: Navigasi & Program -->
                <div>
                    <h4 class="font-extrabold text-base text-slate-900 mb-4 border-b-2 border-brand-accent pb-2 inline-block">Menu & Program</h4>
                    <ul class="space-y-2.5 text-slate-600 text-sm font-medium">
                        <li><a href="#program-kebaikan" class="hover:text-brand-primary transition">Semua Program Kebaikan</a></li>
                        @php
                            $customPages = \App\Models\WebPage::where('is_active', true)->take(4)->get();
                        @endphp
                        @foreach($customPages as $customPage)
                            <li><a href="/halaman/{{ $customPage->slug }}" class="hover:text-brand-primary transition">{{ $customPage->title }}</a></li>
                        @endforeach
                    </ul>
                    <a href="#program-kebaikan" class="inline-block mt-5 border-2 border-brand-primary text-brand-primary font-bold px-5 py-2 rounded-xl hover:bg-brand-primary hover:text-white transition text-xs">
                        Mulai Berdonasi
                    </a>
                </div>

                <!-- Kolom 3: Rekening Donasi Dinamis -->
                <div>
                    <h4 class="font-extrabold text-base text-slate-900 mb-4 border-b-2 border-brand-accent pb-2 inline-block">Rekening Donasi</h4>
                    <div class="space-y-3">
                        @if($homeSetting && !empty($homeSetting->footer_banks))
                            @foreach($homeSetting->footer_banks as $bank)
                                <div x-data="{ copied: false }" class="bg-white p-3 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-3 min-w-0">
                                        @if(!empty($bank['logo']))
                                            <img src="{{ Storage::url($bank['logo']) }}" class="h-8 w-12 object-contain shrink-0" alt="{{ $bank['bank_name'] ?? 'Bank' }}">
                                        @else
                                            <div class="h-8 w-12 bg-slate-100 rounded flex items-center justify-center text-xs font-bold text-slate-500 shrink-0">{{ $bank['bank_name'] ?? 'Bank' }}</div>
                                        @endif
                                        <div class="truncate">
                                            <p class="font-bold text-slate-800 text-xs md:text-sm tracking-wide truncate">{{ $bank['account_number'] ?? '-' }}</p>
                                            <p class="text-[10px] text-slate-500 uppercase truncate">A.N. {{ $bank['account_name'] ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <button
                                        @click="navigator.clipboard.writeText('{{ $bank['account_number'] ?? '' }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="text-xs border px-3 py-1.5 rounded-lg font-bold transition shrink-0"
                                        :class="copied ? 'bg-emerald-50 border-emerald-500 text-emerald-600' : 'border-slate-200 text-slate-700 hover:border-brand-primary hover:text-brand-primary'"
                                    >
                                        <span x-text="copied ? 'Tersalin' : 'Salin'"></span>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm text-slate-500 italic">Belum ada rekening yang ditambahkan.</p>
                        @endif
                    </div>
                </div>

                <!-- Kolom 4: Hubungi Kami -->
                <div>
                    <h4 class="font-extrabold text-base text-slate-900 mb-4 border-b-2 border-brand-accent pb-2 inline-block">Hubungi Kami</h4>
                    <ul class="space-y-3.5 text-sm text-slate-600">
                        @if($homeSetting && $homeSetting->footer_phone)
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-brand-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span class="font-medium">{{ $homeSetting->footer_phone }}</span>
                        </li>
                        @endif
                        
                        @if($homeSetting && $homeSetting->footer_email)
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-brand-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>{{ $homeSetting->footer_email }}</span>
                        </li>
                        @endif
                        
                        @if($homeSetting && $homeSetting->footer_schedule)
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-brand-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ $homeSetting->footer_schedule }}</span>
                        </li>
                        @endif
                        
                        @if($homeSetting && $homeSetting->footer_address)
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-brand-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>{{ $homeSetting->footer_address }}</span>
                        </li>
                        @endif
                    </ul>
                    
                    @if($homeSetting && $homeSetting->footer_gmaps)
                        <div class="mt-5 rounded-xl overflow-hidden shadow-sm h-32 relative border border-slate-200 footer-map">
                            {!! $homeSetting->footer_gmaps !!}
                        </div>
                        <style>
                            .footer-map iframe { width: 100%; height: 100%; border: 0; }
                        </style>
                    @endif
                </div>

            </div>
        </div>
        
        <div class="mt-12 pt-6 border-t border-slate-200/60 text-center text-xs md:text-sm text-slate-500 font-medium">
            <p>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
