@php
    $homeSetting = \App\Models\HomepageSetting::first();
    $siteName = $homeSetting->site_name ?? 'DonasiKu';
    $navbarColor = $homeSetting->navbar_color ?? '#ffffff';

    $currentAmount = (float) ($campaign->current_amount ?? 0);
    $targetAmount = (float) ($campaign->target_amount ?? 0);
    $remainingAmount = max(0, $targetAmount - $currentAmount);
    $percent = $targetAmount > 0 ? min(100, round(($currentAmount / $targetAmount) * 100)) : 0;
    $displayType = $campaign->progress_display_type ?? 'terkumpul';

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
        default => 'Donasi Umum',
    };
@endphp

<div
    x-data="{
        showSticky: false,
        activeTab: 'story',
        copiedLink: false,
        shareCampaign() {
            if (navigator.share) {
                navigator.share({
                    title: @js($campaign->title),
                    url: window.location.href
                });
            } else {
                navigator.clipboard.writeText(window.location.href);
                this.copiedLink = true;
                setTimeout(() => this.copiedLink = false, 2500);
            }
        }
    }"
    @scroll.window="showSticky = (window.scrollY > 360)"
    class="min-h-screen font-sans text-slate-800 pb-28 lg:pb-16"
>
    <!-- TOP NAVBAR (Desktop & Mobile) -->
    <header class="sticky top-0 z-40 border-b border-slate-200/80 backdrop-blur-md" style="background-color: {{ $navbarColor }}f2">
        <div class="max-w-7xl mx-auto px-4 md:px-8 h-16 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <a href="/" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition shrink-0" title="Kembali ke Beranda">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <a href="/" class="hidden sm:flex items-center gap-2 shrink-0">
                    @if($homeSetting && $homeSetting->logo)
                        <img src="{{ Storage::url($homeSetting->logo) }}" alt="{{ $siteName }}" class="h-8 object-contain">
                    @else
                        <span class="font-extrabold text-lg text-slate-900">{{ $siteName }}<span class="text-brand-primary">.</span></span>
                    @endif
                </a>
                <span class="hidden md:inline text-slate-300">/</span>
                <span class="text-xs md:text-sm font-bold text-slate-700 truncate">{{ $campaign->title }}</span>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button @click="shareCampaign()" class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                    <span x-text="copiedLink ? 'Link Tersalin!' : 'Bagikan'"></span>
                </button>
                <a href="/campaign/{{ $campaign->slug }}/checkout" class="hidden sm:inline-flex bg-[#E11D48] hover:bg-rose-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-sm transition">
                    Donasi Sekarang
                </a>
            </div>
        </div>
    </header>

    <!-- MAIN CONTAINER: 2 KOLOM DI DESKTOP, 1 KOLOM DI MOBILE -->
    <main class="max-w-7xl mx-auto px-4 md:px-8 pt-5 md:pt-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

            <!-- ========================================================== -->
            <!-- KOLOM KIRI (7 KOLOM DI DESKTOP): GAMBAR, TAB CERITA, KABAR -->
            <!-- ========================================================== -->
            <div class="lg:col-span-7 space-y-6">
                
                <!-- Cover Image Card -->
                <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="relative w-full aspect-video bg-slate-200">
                        @if($campaign->cover_image)
                            <img src="{{ Storage::url($campaign->cover_image) }}" alt="{{ $campaign->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-400 bg-slate-100 text-sm">Gambar Tidak Tersedia</div>
                        @endif

                        <!-- Badges di atas gambar -->
                        <div class="absolute top-3 left-3 flex flex-wrap gap-2">
                            <span class="px-3 py-1 rounded-xl bg-white/95 backdrop-blur-sm text-brand-primary font-extrabold text-xs shadow-sm">
                                {{ $categoryLabel }}
                            </span>
                            @if($campaign->location)
                                <span class="px-3 py-1 rounded-xl bg-slate-900/75 backdrop-blur-sm text-white font-semibold text-xs flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                                    {{ $campaign->location }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- RINGKASAN KHUSUS MOBILE (Muncul di bawah gambar saat layar < lg) -->
                    <div class="p-5 lg:hidden">
                        <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 mb-3 leading-snug">{{ $campaign->title }}</h1>

                        <div class="flex items-baseline justify-between mb-2">
                            <div>
                                @if($displayType === 'tersisa')
                                    <span class="text-xs text-slate-500 block">Dana Tersisa yang Dibutuhkan</span>
                                    <span class="text-2xl font-extrabold text-brand-primary">Rp {{ number_format($remainingAmount, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-xs text-slate-500 block">Dana Terkumpul</span>
                                    <span class="text-2xl font-extrabold text-brand-primary">Rp {{ number_format($currentAmount, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-blue-50 text-brand-primary">{{ $percent }}%</span>
                        </div>

                        <div class="w-full bg-slate-100 rounded-full h-2.5 mb-2 overflow-hidden">
                            <div class="bg-brand-primary h-2.5 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                        </div>

                        <div class="flex justify-between text-xs text-slate-500 mb-5">
                            <span>Target: <strong class="text-slate-700">Rp {{ number_format($targetAmount, 0, ',', '.') }}</strong></span>
                            <span>
                                @if($daysLeft !== null && $daysLeft >= 0)
                                    <strong class="text-slate-700">{{ $daysLeft }}</strong> hari lagi
                                @else
                                    <strong class="text-slate-700">{{ $campaign->donations->count() }}</strong> Donatur
                                @endif
                            </span>
                        </div>

                        <a href="/campaign/{{ $campaign->slug }}/checkout" class="block w-full text-center bg-[#E11D48] hover:bg-rose-700 text-white font-extrabold text-sm py-3.5 rounded-xl shadow-md shadow-rose-500/20 transition">
                            Donasi Sekarang
                        </a>
                    </div>
                </div>

                <!-- TAB NAVIGASI INTERAKTIF (Cerita, Kabar Terbaru, Donatur, Pencairan) -->
                <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="flex border-b border-slate-100 overflow-x-auto hide-scroll-bar bg-slate-50/60 px-2 pt-2">
                        <button
                            @click="activeTab = 'story'"
                            :class="activeTab === 'story' ? 'border-brand-primary text-brand-primary bg-white shadow-sm' : 'border-transparent text-slate-500 hover:text-slate-800'"
                            class="px-4 py-3 text-xs md:text-sm font-bold border-b-2 rounded-t-xl transition whitespace-nowrap"
                        >
                            Cerita Program
                        </button>
                        <button
                            @click="activeTab = 'updates'"
                            :class="activeTab === 'updates' ? 'border-brand-primary text-brand-primary bg-white shadow-sm' : 'border-transparent text-slate-500 hover:text-slate-800'"
                            class="px-4 py-3 text-xs md:text-sm font-bold border-b-2 rounded-t-xl transition whitespace-nowrap flex items-center gap-1.5"
                        >
                            Kabar Terbaru
                            <span class="px-2 py-0.5 text-[10px] rounded-full bg-blue-50 text-brand-primary font-extrabold">{{ $campaign->updates->count() }}</span>
                        </button>
                        <button
                            @click="activeTab = 'donors'"
                            :class="activeTab === 'donors' ? 'border-brand-primary text-brand-primary bg-white shadow-sm' : 'border-transparent text-slate-500 hover:text-slate-800'"
                            class="px-4 py-3 text-xs md:text-sm font-bold border-b-2 rounded-t-xl transition whitespace-nowrap flex items-center gap-1.5"
                        >
                            Donatur & Doa
                            <span class="px-2 py-0.5 text-[10px] rounded-full bg-emerald-50 text-emerald-600 font-extrabold">{{ $campaign->donations->count() }}</span>
                        </button>
                        <button
                            @click="activeTab = 'withdrawals'"
                            :class="activeTab === 'withdrawals' ? 'border-brand-primary text-brand-primary bg-white shadow-sm' : 'border-transparent text-slate-500 hover:text-slate-800'"
                            class="px-4 py-3 text-xs md:text-sm font-bold border-b-2 rounded-t-xl transition whitespace-nowrap flex items-center gap-1.5"
                        >
                            Pencairan Dana
                            <span class="px-2 py-0.5 text-[10px] rounded-full bg-amber-50 text-amber-600 font-extrabold">{{ $campaign->withdrawals->count() }}</span>
                        </button>
                    </div>

                    <div class="p-5 md:p-8">
                        <!-- TAB 1: CERITA PROGRAM -->
                        <div x-show="activeTab === 'story'" x-data="{ expanded: false }">
                            <div class="relative text-slate-600 leading-relaxed text-sm md:text-[15px] prose max-w-none">
                                <div :class="expanded ? '' : 'max-h-96 overflow-hidden'">
                                    {!! $campaign->description !!}
                                </div>
                                <div x-show="!expanded" class="absolute bottom-0 left-0 w-full h-20 bg-gradient-to-t from-white to-transparent pointer-events-none"></div>
                            </div>
                            <button @click="expanded = !expanded" class="mt-4 w-full text-center py-3 border border-slate-200 rounded-xl font-bold text-slate-700 hover:bg-slate-50 hover:border-brand-primary hover:text-brand-primary transition text-xs md:text-sm">
                                <span x-text="expanded ? 'Ringkas Cerita' : 'Baca Cerita Selengkapnya'"></span>
                            </button>

                            @if($campaign->gmaps_url)
                                <div class="mt-6 pt-6 border-t border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-xs text-slate-600">
                                        <svg class="w-4 h-4 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                        <span>Lokasi Penyaluran: <strong>{{ $campaign->location ?: 'Lihat di Peta' }}</strong></span>
                                    </div>
                                    <a href="{{ $campaign->gmaps_url }}" target="_blank" class="text-xs font-bold text-brand-primary hover:underline">Buka Google Maps ↗</a>
                                </div>
                            @endif
                        </div>

                        <!-- TAB 2: KABAR TERBARU -->
                        <div x-show="activeTab === 'updates'" style="display: none;">
                            @forelse($campaign->updates as $update)
                                <div class="relative pl-6 pb-6 border-l-2 border-blue-200 last:border-0 last:pb-0">
                                    <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-brand-primary border-4 border-white shadow"></div>
                                    <span class="inline-block bg-blue-50 text-brand-primary text-[11px] font-bold px-2.5 py-1 rounded-lg mb-2">
                                        {{ $update->created_at->translatedFormat('d F Y') }}
                                    </span>
                                    <h3 class="font-bold text-slate-900 text-base mb-2">{{ $update->title }}</h3>
                                    <div class="prose prose-sm text-slate-600 max-w-none">
                                        {!! $update->content !!}
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10 text-slate-400">
                                    <p class="text-sm font-medium">Belum ada kabar penyaluran terbaru untuk program ini.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- TAB 3: DONATUR & DOA -->
                        <div x-show="activeTab === 'donors'" style="display: none;">
                            <div class="space-y-3">
                                @forelse($campaign->donations->take(10) as $donasi)
                                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-start justify-between gap-4">
                                        <div class="flex items-start gap-3">
                                            <div class="w-10 h-10 bg-blue-100 text-brand-primary rounded-full flex items-center justify-center font-extrabold text-sm shrink-0">
                                                {{ strtoupper(substr($donasi->donor_name ?? 'H', 0, 1)) }}
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-slate-900 text-sm">{{ $donasi->donor_name ?: 'Hamba Allah' }}</h4>
                                                <span class="text-[11px] text-slate-400 block mb-1.5">{{ $donasi->created_at->diffForHumans() }}</span>
                                                <p class="text-xs text-slate-600 italic">"Semoga menjadi amal jariyah dan membawa keberkahan bagi sesama. Aamiin."</p>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="text-xs md:text-sm font-extrabold text-brand-primary">Rp {{ number_format($donasi->amount ?? 0, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-10 text-slate-400">
                                        <p class="text-sm">Belum ada donatur. Jadilah orang baik pertama yang berdonasi!</p>
                                    </div>
                                @endforelse
                            </div>

                            @if($campaign->donations->count() > 10)
                                <a href="/campaign/{{ $campaign->slug }}/donations" class="mt-4 block w-full text-center py-3 rounded-xl border border-slate-200 text-xs font-bold text-brand-primary hover:bg-slate-50 transition">
                                    Lihat Semua Donatur ({{ $campaign->donations->count() }})
                                </a>
                            @endif
                        </div>

                        <!-- TAB 4: PENCAIRAN DANA (TRANSPARANSI) -->
                        <div x-show="activeTab === 'withdrawals'" style="display: none;">
                            <div class="space-y-4">
                                @forelse($campaign->withdrawals as $wd)
                                    <div class="p-4 bg-amber-50/50 border border-amber-200/80 rounded-2xl">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-bold text-amber-800 bg-amber-100 px-2.5 py-1 rounded-lg">
                                                Dicairkan pada {{ $wd->created_at->translatedFormat('d M Y') }}
                                            </span>
                                            <span class="text-sm font-extrabold text-emerald-600">
                                                Rp {{ number_format($wd->amount ?? 0, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-700 font-medium mt-2">
                                            <strong>Tujuan Penyaluran:</strong> {{ $wd->purpose ?: 'Penyaluran program kebaikan' }}
                                        </p>
                                    </div>
                                @empty
                                    <div class="text-center py-10 text-slate-400">
                                        <p class="text-sm">Belum ada pencairan dana pada program ini.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PROGRAM KEBAIKAN LAINNYA (CROSS-SELLING) -->
                @if($otherCampaigns->count() > 0)
                    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-slate-100 p-5 md:p-8">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="text-lg font-extrabold text-slate-900">Program Kebaikan Lainnya</h2>
                            <a href="/" class="text-xs font-bold text-brand-primary hover:underline">Lihat Semua</a>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            @foreach($otherCampaigns as $other)
                                @php
                                    $pct = $other->target_amount > 0 ? min(100, round(($other->current_amount / $other->target_amount) * 100)) : 0;
                                @endphp
                                <a href="/campaign/{{ $other->slug }}" class="bg-white rounded-2xl border border-slate-100 hover:border-brand-primary hover:shadow-md transition-all overflow-hidden flex flex-col group">
                                    <div class="h-32 bg-slate-100 w-full shrink-0 overflow-hidden">
                                        @if($other->cover_image)
                                            <img src="{{ Storage::url($other->cover_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-xs text-slate-400">No Image</div>
                                        @endif
                                    </div>
                                    <div class="p-3.5 flex flex-col flex-grow">
                                        <h3 class="font-bold text-slate-900 text-xs line-clamp-2 mb-3 group-hover:text-brand-primary transition">{{ $other->title }}</h3>
                                        <div class="mt-auto">
                                            <div class="w-full bg-slate-100 rounded-full h-1.5 mb-2 overflow-hidden">
                                                <div class="bg-brand-primary h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                                            </div>
                                            <span class="text-xs font-extrabold text-brand-primary">Rp {{ number_format($other->current_amount ?? 0, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            <!-- ========================================================== -->
            <!-- KOLOM KANAN (5 KOLOM STICKY DI DESKTOP): KARTU DONASI & PENGGALANG -->
            <!-- ========================================================== -->
            <aside class="lg:col-span-5 lg:sticky lg:top-24 space-y-5">
                
                <!-- Kartu Utama Target & CTA (Desktop Primary Card) -->
                <div class="bg-white rounded-3xl shadow-md border border-slate-100 p-6 md:p-7">
                    <div class="hidden lg:block">
                        <span class="inline-block px-3 py-1 rounded-full bg-blue-50 text-brand-primary font-bold text-xs mb-3">
                            {{ $categoryLabel }}
                        </span>
                        <h1 class="text-xl font-extrabold text-slate-900 leading-snug mb-5">{{ $campaign->title }}</h1>

                        <!-- Angka Utama (Terkumpul / Tersisa) -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 mb-5">
                            @if($displayType === 'tersisa')
                                <span class="text-xs font-semibold text-slate-500 block mb-0.5">Dana Tersisa yang Dibutuhkan</span>
                                <span class="text-2xl md:text-3xl font-extrabold text-brand-primary block">
                                    Rp {{ number_format($remainingAmount, 0, ',', '.') }}
                                </span>
                                <span class="text-xs text-slate-400 mt-1 block">
                                    Terkumpul Rp {{ number_format($currentAmount, 0, ',', '.') }} dari target Rp {{ number_format($targetAmount, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-xs font-semibold text-slate-500 block mb-0.5">Dana Terkumpul</span>
                                <span class="text-2xl md:text-3xl font-extrabold text-brand-primary block">
                                    Rp {{ number_format($currentAmount, 0, ',', '.') }}
                                </span>
                                <span class="text-xs text-slate-400 mt-1 block">
                                    dari target Rp {{ number_format($targetAmount, 0, ',', '.') }}
                                </span>
                            @endif

                            <!-- Progress Bar -->
                            <div class="w-full bg-slate-200 rounded-full h-2.5 mt-4 mb-2 overflow-hidden">
                                <div class="bg-brand-primary h-2.5 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                            </div>

                            <div class="flex justify-between items-center text-xs text-slate-600 font-semibold">
                                <span>{{ $percent }}% Tercapai</span>
                                <span>
                                    @if($daysLeft !== null && $daysLeft >= 0)
                                        ⏳ {{ $daysLeft }} Hari Lagi
                                    @elseif($daysLeft === -1)
                                        Program Berakhir
                                    @else
                                        Tanpa Batas Waktu
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- 3 Statistik Ringkas -->
                        <div class="grid grid-cols-3 gap-2 text-center mb-6">
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="block text-base font-extrabold text-slate-900">{{ $campaign->donations->count() }}</span>
                                <span class="text-[11px] text-slate-500 font-medium">Donatur</span>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="block text-base font-extrabold text-slate-900">{{ $campaign->updates->count() }}</span>
                                <span class="text-[11px] text-slate-500 font-medium">Kabar</span>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="block text-base font-extrabold text-slate-900">{{ $campaign->withdrawals->count() }}</span>
                                <span class="text-[11px] text-slate-500 font-medium">Pencairan</span>
                            </div>
                        </div>

                        <!-- Tombol Donasi Sekarang (Desktop) -->
                        <a href="/campaign/{{ $campaign->slug }}/checkout" class="block w-full text-center bg-[#E11D48] hover:bg-rose-700 text-white font-extrabold text-base py-4 rounded-2xl shadow-lg shadow-rose-500/25 hover:-translate-y-0.5 transition-all mb-3">
                            Donasi Sekarang
                        </a>

                        <button @click="shareCampaign()" class="w-full py-3 rounded-2xl border border-slate-200 hover:bg-slate-50 font-bold text-xs text-slate-700 flex items-center justify-center gap-2 transition">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                            <span x-text="copiedLink ? 'Tautan Berhasil Disalin!' : 'Bagikan Program Kebaikan Ini'"></span>
                        </button>
                    </div>

                    <!-- Profil Penggalang Dana (Tampil di Mobile & Desktop) -->
                    <div class="lg:mt-6 lg:pt-6 lg:border-t border-slate-100">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-3">Informasi Penggalang Dana</p>
                        <div class="flex items-center gap-3.5 p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                            <div class="w-12 h-12 bg-brand-primary text-white rounded-2xl flex items-center justify-center font-extrabold text-lg shrink-0 shadow-sm">
                                {{ strtoupper(substr($campaign->user->name ?? 'D', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <p class="font-extrabold text-slate-900 text-sm truncate">{{ $campaign->user->name ?? $siteName }}</p>
                                    <svg class="w-4 h-4 text-blue-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">Identitas & Rekening Terverifikasi</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Doa-doa Orang Baik Terkini (Sidebar Card) -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-base font-extrabold text-slate-900">Doa-doa Orang Baik</h2>
                        <a href="/campaign/{{ $campaign->slug }}/donations" class="text-xs font-bold text-brand-primary hover:underline">Lihat Semua</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($campaign->donations->take(3) as $donasi)
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                                <div class="flex items-center justify-between mb-1.5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 bg-blue-100 text-brand-primary rounded-full flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($donasi->donor_name ?? 'H', 0, 1)) }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-xs">{{ $donasi->donor_name ?: 'Hamba Allah' }}</h4>
                                            <span class="text-[10px] text-slate-400 block">{{ $donasi->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold text-brand-primary">Rp {{ number_format($donasi->amount ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <p class="text-xs text-slate-600 italic mt-1">"Semoga donasi ini bermanfaat dan menjadi berkah bagi semuanya. Aamiin."</p>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400 text-xs border border-dashed border-slate-200 rounded-2xl bg-slate-50">
                                Belum ada doa. Jadilah yang pertama berdonasi.
                            </div>
                        @endforelse
                    </div>
                </div>

            </aside>

        </div>
    </main>

    <!-- STICKY BOTTOM BAR KHUSUS MOBILE (< lg) -->
    <div
        x-show="showSticky"
        style="display: none;"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="lg:hidden fixed bottom-0 inset-x-0 z-50 bg-white/95 backdrop-blur-md border-t border-slate-200 p-3 flex gap-2.5 shadow-[0_-8px_20px_rgba(0,0,0,0.06)]"
    >
        <button @click="shareCampaign()" class="flex-none bg-white border border-slate-200 text-slate-700 font-bold px-4 py-3 rounded-xl flex items-center justify-center hover:bg-slate-50 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
        </button>

        <a href="/campaign/{{ $campaign->slug }}/checkout" class="flex-grow text-center bg-[#E11D48] hover:bg-rose-700 text-white font-extrabold text-sm py-3.5 rounded-xl shadow-md transition-colors">
            Donasi Sekarang
        </a>
    </div>
</div>
