<div class="w-full" x-data="{
        viewMode: 'carousel',
        scrollNextCamp() { $refs.campaignSlider.scrollBy({left: 360, behavior: 'smooth'}) },
        scrollPrevCamp() { $refs.campaignSlider.scrollBy({left: -360, behavior: 'smooth'}) }
    }">
    <div class="mt-10 md:mt-14 px-4 md:px-8 relative max-w-7xl mx-auto">
        
        <!-- Header Section: Title + Search & Controls -->
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 mb-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-brand-primary text-xs font-bold mb-2">
                    <span class="w-2 h-2 rounded-full bg-brand-primary animate-pulse"></span>
                    Program Kebaikan
                </div>
                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $title }}</h2>
                @if($subtitle)
                    <p class="text-sm md:text-base text-slate-500 mt-1">{{ $subtitle }}</p>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @if($showFilter)
                    <!-- Live Search Input -->
                    <div class="relative flex-grow sm:flex-grow-0 sm:w-64">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Cari program kebaikan..."
                            class="w-full pl-10 pr-8 py-2.5 bg-white border border-slate-200 rounded-xl text-xs md:text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-primary/30 focus:border-brand-primary shadow-sm transition"
                        >
                        @if($search)
                            <button wire:click="$set('search', '')" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        @endif
                    </div>
                @endif

                <!-- Toggle Carousel / Grid View -->
                <div class="hidden sm:inline-flex bg-slate-100 p-1 rounded-xl border border-slate-200">
                    <button @click="viewMode = 'carousel'" :class="viewMode === 'carousel' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                        Geser
                    </button>
                    <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Semua
                    </button>
                </div>

                <!-- Navigasi Panah Carousel -->
                <div x-show="viewMode === 'carousel'" class="hidden md:flex gap-2">
                    <button @click="scrollPrevCamp()" class="bg-white border border-slate-200 hover:border-brand-primary hover:text-brand-primary text-slate-600 rounded-xl p-2.5 shadow-sm transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button @click="scrollNextCamp()" class="bg-white border border-slate-200 hover:border-brand-primary hover:text-brand-primary text-slate-600 rounded-xl p-2.5 shadow-sm transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter Kategori Pills (Jika section diset ke 'semua') -->
        @if($category === 'semua' && $showFilter)
            @php
                $categories = [
                    'semua' => 'Semua Program',
                    'donasi' => 'Donasi Umum',
                    'zakat' => 'Zakat',
                    'infaq' => 'Infaq',
                    'sedekah' => 'Sedekah',
                    'wakaf' => 'Wakaf',
                ];
            @endphp
            <div class="flex items-center gap-2 overflow-x-auto hide-scroll-bar pb-4 mb-2">
                @foreach($categories as $key => $label)
                    <button
                        wire:click="selectCategory('{{ $key }}')"
                        class="shrink-0 px-4 py-2 rounded-full text-xs md:text-sm font-bold transition-all duration-200 {{ $selectedCategory === $key ? 'bg-brand-primary text-white shadow-md shadow-blue-500/20' : 'bg-white text-slate-600 border border-slate-200 hover:border-brand-primary hover:text-brand-primary' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        @endif

        <!-- Daftar Kartu Campaign -->
        <div
            x-ref="campaignSlider"
            :class="viewMode === 'carousel'
                ? 'flex overflow-x-auto gap-4 md:gap-6 pb-6 snap-x snap-mandatory hide-scroll-bar'
                : 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-6 pb-6'"
        >
            @forelse($campaigns as $campaign)
                @php
                    $percent = $campaign->target_amount > 0 ? min(100, round(($campaign->current_amount / $campaign->target_amount) * 100)) : 0;
                    $daysLeft = null;
                    if ($campaign->end_date) {
                        $diff = now()->startOfDay()->diffInDays($campaign->end_date->startOfDay(), false);
                        $daysLeft = $diff >= 0 ? $diff : -1;
                    }
                    $categoryLabel = match($campaign->category) {
                        'zakat' => 'Zakat',
                        'infaq' => 'Infaq',
                        'sedekah' => 'Sedekah',
                        'wakaf' => 'Wakaf',
                        default => 'Donasi',
                    };
                @endphp

                <a
                    href="/campaign/{{ $campaign->slug }}"
                    :class="viewMode === 'carousel' ? 'shrink-0 w-[78vw] sm:w-[320px] md:w-[31.5%] snap-start' : 'w-full'"
                    class="bg-white rounded-2xl shadow-sm border border-slate-100 hover:shadow-xl hover:border-blue-200 hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col group cursor-pointer"
                >
                    <!-- Gambar Cover + Badge -->
                    <div class="relative h-44 md:h-52 overflow-hidden bg-slate-100">
                        @if($campaign->cover_image)
                            <img src="{{ Storage::url($campaign->cover_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" alt="{{ $campaign->title }}">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200 flex flex-col items-center justify-center text-slate-400">
                                <svg class="w-10 h-10 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-xs font-medium">Gambar Belum Tersedia</span>
                            </div>
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-60"></div>

                        <!-- Badge Kategori -->
                        <div class="absolute top-3 left-3 flex items-center gap-1.5">
                            <span class="px-2.5 py-1 rounded-lg bg-white/95 backdrop-blur-sm text-slate-800 font-bold text-[11px] shadow-sm">
                                {{ $categoryLabel }}
                            </span>
                            @if($campaign->location)
                                <span class="px-2.5 py-1 rounded-lg bg-slate-900/75 backdrop-blur-sm text-white font-medium text-[10px] flex items-center gap-1">
                                    <svg class="w-3 h-3 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                                    <span class="truncate max-w-[100px]">{{ $campaign->location }}</span>
                                </span>
                            @endif
                        </div>

                        <!-- Badge Sisa Waktu -->
                        @if($daysLeft !== null)
                            <div class="absolute bottom-3 right-3">
                                @if($daysLeft > 0)
                                    <span class="px-2.5 py-1 rounded-lg bg-black/70 backdrop-blur-sm text-white font-semibold text-[11px] flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $daysLeft }} hari lagi
                                    </span>
                                @elseif($daysLeft === 0)
                                    <span class="px-2.5 py-1 rounded-lg bg-rose-600/90 text-white font-bold text-[11px]">Hari Terakhir!</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-700/90 text-white font-medium text-[11px]">Selesai</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Isi Kartu -->
                    <div class="p-4 md:p-5 flex flex-col flex-grow">
                        <!-- Penggalang Dana -->
                        <div class="flex items-center gap-2 mb-2.5">
                            <div class="w-5 h-5 bg-blue-50 text-brand-primary rounded-full flex items-center justify-center font-bold text-[10px] shrink-0">
                                {{ strtoupper(substr($campaign->user->name ?? 'D', 0, 1)) }}
                            </div>
                            <span class="text-xs text-slate-600 font-semibold truncate">{{ $campaign->user->name ?? 'DonasiKu Official' }}</span>
                            <svg class="w-4 h-4 text-blue-500 shrink-0" fill="currentColor" viewBox="0 0 20 20" title="Terverifikasi"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        </div>

                        <!-- Judul Campaign -->
                        <h3 class="font-bold text-slate-900 text-sm md:text-base line-clamp-2 mb-4 group-hover:text-brand-primary transition-colors leading-snug">
                            {{ $campaign->title }}
                        </h3>

                        <!-- Progress & Statistik Bawah -->
                        <div class="mt-auto pt-3 border-t border-slate-100">
                            <!-- Progress Bar -->
                            <div class="w-full bg-slate-100 rounded-full h-2 mb-2.5 overflow-hidden">
                                <div class="bg-brand-primary h-2 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                            </div>

                            <div class="flex justify-between items-end">
                                <div>
                                    <span class="text-[11px] text-slate-400 font-medium block">Terkumpul</span>
                                    <span class="text-sm md:text-base font-extrabold text-brand-primary">
                                        Rp {{ number_format($campaign->current_amount ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[11px] text-slate-400 font-medium block">Donatur</span>
                                    <span class="text-xs font-bold text-slate-700">
                                        {{ $campaign->donations_count ?? 0 }} Orang Baik
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-slate-500 py-12 px-4 w-full text-center border-2 border-dashed border-slate-200 rounded-2xl bg-white">
                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <p class="font-bold text-slate-700 text-sm">Program tidak ditemukan</p>
                    <p class="text-xs text-slate-400 mt-1">Coba kata kunci atau kategori kebaikan lainnya.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
