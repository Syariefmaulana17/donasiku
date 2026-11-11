@php
    $homeSetting = \App\Models\HomepageSetting::first();
    $navbarColor = $homeSetting->navbar_color ?? '#ffffff';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Donasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .hide-scroll-bar { -ms-overflow-style: none; scrollbar-width: none; }
        .hide-scroll-bar::-webkit-scrollbar { display: none; }
        .prose h2 { font-size: 1.5rem; font-weight: bold; margin-top: 1.5rem; margin-bottom: 1rem; }
        .prose p { margin-bottom: 1rem; line-height: 1.75; }
        .prose a { color: #2563eb; text-decoration: underline; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">
    
    <!-- HEADER / NAVBAR -->
    <nav x-data="{ open: false }" class="shadow-sm border-b border-gray-100 p-4 relative z-50" style="background-color: {{ $navbarColor }}">
        <div class="container mx-auto flex justify-between items-center max-w-6xl">
            <a href="/" class="flex items-center">
                @if($homeSetting && $homeSetting->logo)
                    <img src="{{ Storage::url($homeSetting->logo) }}" alt="Logo" class="h-10 object-contain">
                @else
                    <span class="text-2xl font-black text-blue-600 tracking-tight">DonasiKu 💙</span>
                @endif
            </a>

            <div class="hidden md:flex items-center space-x-3">
                @if($homeSetting && $homeSetting->navbar_menu)
                    @foreach($homeSetting->navbar_menu as $menu)
                        <a href="{{ $menu['url'] }}" class="border-2 border-blue-600 text-blue-600 px-5 py-2 rounded-xl hover:bg-blue-50 font-bold transition text-[14px]">
                            {{ $menu['label'] }}
                        </a>
                    @endforeach
                @endif
                @auth <a href="/admin" class="bg-blue-600 text-white px-5 py-2 rounded-xl shadow-sm hover:bg-blue-700 font-bold transition text-[14px]">Ruang Kendali</a> @endauth
            </div>

            <div class="md:hidden flex items-center">
                <button @click="open = !open" class="text-gray-600 focus:outline-none p-2 bg-white/50 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path><path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display: none;"></path></svg>
                </button>
            </div>
        </div>

        <div x-show="open" class="md:hidden absolute top-full left-0 w-full bg-white shadow-xl flex flex-col p-5 space-y-3" style="display: none;">
            @if($homeSetting && $homeSetting->navbar_menu)
                @foreach($homeSetting->navbar_menu as $menu)
                    <a href="{{ $menu['url'] }}" class="block text-center border-2 border-blue-600 text-blue-600 px-4 py-3 rounded-xl font-bold">{{ $menu['label'] }}</a>
                @endforeach
            @endif
        </div>
    </nav>

    <!-- CONTENT / HALAMAN UTAMA -->
    <main class="flex-grow">
        @if($homeSetting && $homeSetting->dynamic_sections)
            @foreach($homeSetting->dynamic_sections as $block)
                
                {{-- 1. BLOK BANNER SLIDER --}}
                @if($block['type'] === 'banner_slider')
                    @if(!empty($block['data']['slides']))
                        <div x-data="{
                                scrollNext() { $refs.bannerSlider.scrollBy({left: 400, behavior: 'smooth'}) },
                                scrollPrev() { $refs.bannerSlider.scrollBy({left: -400, behavior: 'smooth'}) }
                            }" class="relative px-4 md:px-8 mt-4 md:mt-8 max-w-7xl mx-auto">
                            
                            <button @click="scrollPrev()" class="hidden md:flex absolute left-4 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white text-blue-600 rounded-full p-2 shadow-lg transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                            </button>

                            <div x-ref="bannerSlider" class="flex overflow-x-auto snap-x snap-mandatory hide-scroll-bar gap-4 md:gap-6">
                                @foreach($block['data']['slides'] as $slide)
                                <div class="shrink-0 w-full snap-center relative rounded-2xl overflow-hidden shadow-sm h-56 md:h-[400px]">
                                    <a href="{{ $slide['link_url'] ?? '#' }}" class="block w-full h-full">
                                        <img src="{{ Storage::url($slide['image']) }}" class="w-full h-full object-cover" alt="Banner Slide">
                                    </a>
                                </div>
                                @endforeach
                            </div>

                            <button @click="scrollNext()" class="hidden md:flex absolute right-4 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white text-blue-600 rounded-full p-2 shadow-lg transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                    @endif

                {{-- 2. BLOK TOMBOL KATEGORI --}}
                @elseif($block['type'] === 'category_buttons')
                    @if(!empty($block['data']['buttons']))
                        <div class="bg-white rounded-2xl shadow-sm mx-4 md:mx-8 mt-8 p-4 md:p-6 border border-gray-100 max-w-7xl md:mx-auto">
                            <div class="grid grid-cols-4 gap-2 md:gap-6 text-center">
                                @foreach($block['data']['buttons'] as $cat)
                                <a href="{{ $cat['link'] ?? '#' }}" class="flex flex-col items-center cursor-pointer hover:scale-105 transition-transform block">
                                    <div class="w-12 h-12 md:w-16 md:h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-2 md:mb-3">
                                        @if(!empty($cat['icon_svg']))
                                            {!! $cat['icon_svg'] !!}
                                        @else
                                            <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @endif
                                    </div>
                                    <span class="text-[10px] md:text-sm font-semibold text-gray-700 leading-tight">{{ $cat['title'] ?? '' }}</span>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                
                {{-- 3. BLOK BANNER STATIS (1 Gambar) --}}
                @elseif($block['type'] === 'banner_statis')
                    <div class="max-w-6xl mx-auto px-4 mt-8">
                        <a href="{{ $block['data']['link_url'] ?? '#' }}" class="block rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition">
                            <img src="{{ Storage::url($block['data']['image']) }}" class="w-full h-auto object-cover" alt="Banner Statis">
                        </a>
                    </div>
                
                {{-- 4. BLOK DAFTAR CAMPAIGN --}}
                @elseif($block['type'] === 'campaign_list')
                    <livewire:campaign-search 
                        :title="$block['data']['title']" 
                        :subtitle="$block['data']['subtitle'] ?? ''" 
                        :category="$block['data']['category_filter'] ?? 'semua'" 
                        wire:key="block-{{ $loop->index }}" 
                    />
                
                {{-- 5. BLOK DAFTAR ARTIKEL (Placeholder) --}}
                @elseif($block['type'] === 'article_list')
                    <div class="max-w-6xl mx-auto px-4 mt-12 mb-8 text-center">
                        <h2 class="text-2xl font-bold">{{ $block['data']['title'] }}</h2>
                        <p class="text-gray-500">{{ $block['data']['subtitle'] ?? '' }}</p>
                        <div class="p-8 border-2 border-dashed border-gray-300 rounded-xl mt-6 text-gray-400">
                            Fitur Daftar Artikel sedang dalam pengembangan 🚀
                        </div>
                    </div>

                {{-- 6. BLOK TEKS HTML --}}
                @elseif($block['type'] === 'html_content')
                    <div class="max-w-6xl mx-auto px-4 mt-8 mb-8 prose">
                        {!! $block['data']['content'] !!}
                    </div>
                @endif

            @endforeach
        @endif
    </main>

    <!-- FOOTER BARU (Desain Profesional) -->
    <footer class="border-t border-gray-200 py-12 mt-12" style="background-color: {{ $homeSetting->footer_bg_color ?? '#fff7ed' }}">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 md:gap-12">

                <!-- Kolom 1: Identitas & Deskripsi -->
                <div>
                    @if($homeSetting && $homeSetting->footer_logo)
                        <img src="{{ Storage::url($homeSetting->footer_logo) }}" alt="Logo Footer" class="h-16 object-contain mb-4">
                    @else
                        <h3 class="text-2xl font-black text-blue-600 mb-4">DonasiKu</h3>
                    @endif
                    <p class="text-gray-600 text-sm leading-relaxed">
                        {{ $homeSetting->footer_desc ?? 'Mari bersinergi dalam aksi nyata untuk kebaikan bersama. Langkah kecil kepedulian Anda adalah dunia baru bagi mereka.' }}
                    </p>
                </div>

                <!-- Kolom 2: Rekomendasi Program (Bisa disesuaikan nanti lewat menu) -->
                <div>
                    <h4 class="font-bold text-lg text-gray-800 mb-4 border-b-2 border-orange-400 pb-2 inline-block">Rekomendasi Program</h4>
                    <ul class="space-y-3 text-gray-600 text-sm font-medium">
                        <li><a href="#" class="hover:text-blue-600 transition">Donasi Palestina</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition">Sedekah Quran</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition">Daftar Donatur Rutin</a></li>
                    </ul>
                    <a href="/" class="inline-block mt-6 border-2 border-orange-400 text-orange-500 font-bold px-6 py-2 rounded-full hover:bg-orange-50 transition text-sm">Lihat Semua Program</a>
                </div>

                <!-- Kolom 3: Rekening Donasi Dinamis -->
                <div>
                    <h4 class="font-bold text-lg text-gray-800 mb-4 border-b-2 border-orange-400 pb-2 inline-block">Rekening Donasi</h4>
                    <div class="space-y-3">
                        @if($homeSetting && !empty($homeSetting->footer_banks))
                            @foreach($homeSetting->footer_banks as $bank)
                                <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        @if(!empty($bank['logo']))
                                            <img src="{{ Storage::url($bank['logo']) }}" class="h-8 w-12 object-contain" alt="{{ $bank['bank_name'] ?? 'Bank' }}">
                                        @else
                                            <div class="h-8 w-12 bg-gray-100 rounded flex items-center justify-center text-xs font-bold text-gray-400">{{ $bank['bank_name'] ?? 'Bank' }}</div>
                                        @endif
                                        <div>
                                            <p class="font-bold text-gray-800 text-sm tracking-wide">{{ $bank['account_number'] ?? '-' }}</p>
                                            <p class="text-[10px] text-gray-500 uppercase">A.N. {{ $bank['account_name'] ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <button class="text-xs border border-orange-400 text-orange-500 px-3 py-1.5 rounded-lg hover:bg-orange-50 font-semibold transition" onclick="navigator.clipboard.writeText('{{ $bank['account_number'] ?? '' }}'); alert('Nomor Rekening disalin!');">Salin</button>
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-500 italic">Belum ada rekening yang ditambahkan.</p>
                        @endif
                    </div>
                </div>

                <!-- Kolom 4: Hubungi Kami -->
                <div>
                    <h4 class="font-bold text-lg text-gray-800 mb-4 border-b-2 border-orange-400 pb-2 inline-block">Hubungi Kami</h4>
                    <ul class="space-y-4 text-sm text-gray-600">
                        @if($homeSetting && $homeSetting->footer_phone)
                        <li class="flex items-start gap-3">
                            <!-- Icon WhatsApp/Telp -->
                            <svg class="w-5 h-5 text-orange-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span class="font-medium">{{ $homeSetting->footer_phone }}</span>
                        </li>
                        @endif
                        
                        @if($homeSetting && $homeSetting->footer_email)
                        <li class="flex items-start gap-3">
                            <!-- Icon Email -->
                            <svg class="w-5 h-5 text-orange-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>{{ $homeSetting->footer_email }}</span>
                        </li>
                        @endif
                        
                        @if($homeSetting && $homeSetting->footer_schedule)
                        <li class="flex items-start gap-3">
                            <!-- Icon Jam -->
                            <svg class="w-5 h-5 text-orange-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ $homeSetting->footer_schedule }}</span>
                        </li>
                        @endif
                        
                        @if($homeSetting && $homeSetting->footer_address)
                        <li class="flex items-start gap-3">
                            <!-- Icon Lokasi -->
                            <svg class="w-5 h-5 text-orange-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>{{ $homeSetting->footer_address }}</span>
                        </li>
                        @endif
                    </ul>
                    
                    @if($homeSetting && $homeSetting->footer_gmaps)
                        <div class="mt-5 rounded-xl overflow-hidden shadow-sm h-32 relative border border-gray-200">
                            <!-- Script render iframe Google Maps (jika user memasukkan iframe) -->
                            {!! $homeSetting->footer_gmaps !!}
                        </div>
                        <style>
                            /* Menyesuaikan iframe maps agar fit ke dalam kotak */
                            .mt-5 iframe { width: 100%; height: 100%; border: 0; }
                        </style>
                    @endif
                </div>

            </div>
        </div>
        
        <!-- Copyright Section -->
        <div class="mt-12 pt-6 border-t border-gray-200/50 text-center text-sm text-gray-500 font-medium">
            <p>&copy; {{ date('Y') }} DonasiKu. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>