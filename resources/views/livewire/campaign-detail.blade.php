<!-- x-data mendeteksi posisi scroll untuk memunculkan tombol bawah -->
<div x-data="{ showSticky: false }" 
     @scroll.window="showSticky = (window.scrollY > ($refs.mainButton?.offsetTop || 400))" 
     class="bg-gray-100 min-h-screen font-sans text-gray-800 antialiased">
     
    <!-- CSS Tambahan untuk menyembunyikan scrollbar -->
    <style>
        .hide-scroll-bar { -ms-overflow-style: none; scrollbar-width: none; }
        .hide-scroll-bar::-webkit-scrollbar { display: none; }
    </style>

    <!-- Kontainer Utama: Dikunci di 480px agar seragam dengan tombol bawah -->
    <div class="max-w-[480px] mx-auto bg-white min-h-screen shadow-2xl pb-32 relative border-x border-gray-200">
        
        <!-- Area Cover & Tombol Kembali -->
        <div class="relative w-full aspect-video bg-gray-200">
            <div class="absolute top-4 left-4 z-10">
                <a href="/" class="bg-black/50 text-white p-2.5 rounded-full inline-block hover:bg-black/70 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
            </div>
            
            @if($campaign->cover_image)
                <img src="{{ Storage::url($campaign->cover_image) }}" alt="Cover" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-500 bg-gray-300">Gambar Tidak Tersedia</div>
            @endif
        </div>

        <!-- KONTEN UTAMA DIMULAI DI SINI -->
        <div class="p-5">
            
            <h1 class="text-[19px] font-bold text-gray-900 mb-3 leading-snug">{{ $campaign->title }}</h1>
            
            <div class="mb-4">
                <span class="text-2xl font-bold text-blue-600 block mb-0.5">Rp {{ number_format($campaign->current_amount ?? 0, 0, ',', '.') }}</span>
                <span class="text-xs text-gray-500">Terkumpul dari target Rp {{ number_format($campaign->target_amount ?? 0, 0, ',', '.') }}</span>
            </div>
            
            @php $percent = $campaign->target_amount > 0 ? min(100, ($campaign->current_amount / $campaign->target_amount) * 100) : 0; @endphp
            <div class="w-full bg-gray-100 rounded-full h-2 mb-6 overflow-hidden">
                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $percent }}%"></div>
            </div>

            <!-- TOMBOL DONASI UTAMA (Di Bawah Progress Bar) -->
            <div x-ref="mainButton" class="w-full mb-8">
                <a href="/campaign/{{ $campaign->slug }}/checkout" class="block w-full text-center bg-[#E11D48] hover:bg-rose-700 text-white font-bold text-[15px] py-3.5 rounded-xl shadow-md transition-colors cursor-pointer">
                    Donasi Sekarang
                </a>
            </div>

            <!-- Profil Penggalang Dana -->
            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100 mb-8">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-lg shrink-0">
                    {{ substr($campaign->user->name ?? 'A', 0, 1) }}
                </div>
                <div>
                    <p class="text-[11px] text-gray-500 mb-0.5">Penggalang Dana</p>
                    <div class="flex items-center gap-1.5">
                        <p class="font-bold text-gray-900 text-[13px]">{{ $campaign->user->name ?? 'Anonim' }}</p>
                        <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Cerita Program -->
            <div class="mb-10" x-data="{ expanded: false }">
                <h2 class="text-lg font-bold mb-3 text-gray-900">Cerita Program</h2>
                <div class="relative text-gray-600 leading-relaxed text-[13px]">
                    <div :class="expanded ? '' : 'line-clamp-4 overflow-hidden'">
                        {!! $campaign->description !!}
                    </div>
                    <div x-show="!expanded" class="absolute bottom-0 left-0 w-full h-12 bg-gradient-to-t from-white to-transparent pointer-events-none"></div>
                </div>
                <button @click="expanded = !expanded" class="mt-3 w-full text-center py-2.5 border border-gray-200 rounded-xl font-bold text-gray-700 hover:bg-gray-50 transition text-[13px]">
                    <span x-text="expanded ? 'Tutup Cerita' : 'Baca Selengkapnya'"></span>
                </button>
            </div>

            <!-- 4 Menu Informasi (Melempar ke Halaman Lain) -->
            <h2 class="text-lg font-bold mb-3 text-gray-900">Informasi Tambahan</h2>
            <div class="flex flex-col gap-3 mb-10">
                
                <a href="/campaign/{{ $campaign->slug }}/donations" class="flex items-center justify-between p-3.5 bg-white border border-gray-100 hover:border-blue-300 hover:shadow-sm rounded-2xl transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center group-hover:bg-green-100 transition shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-900 text-[13px] mb-0.5">Donatur</span>
                            <span class="text-[11px] text-gray-500">{{ $campaign->donations->count() }} Orang baik berdonasi</span>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>

                <a href="/campaign/{{ $campaign->slug }}/updates" class="flex items-center justify-between p-3.5 bg-white border border-gray-100 hover:border-blue-300 hover:shadow-sm rounded-2xl transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"></path></svg>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-900 text-[13px] mb-0.5">Kabar Terbaru</span>
                            <span class="text-[11px] text-gray-500">{{ $campaign->updates->count() }} Pembaruan disalurkan</span>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>

                <a href="#" class="flex items-center justify-between p-3.5 bg-white border border-gray-100 hover:border-blue-300 hover:shadow-sm rounded-2xl transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center group-hover:bg-orange-100 transition shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-900 text-[13px] mb-0.5">Pencairan Dana</span>
                            <span class="text-[11px] text-gray-500">Transparansi dana keluar</span>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>

                <a href="/campaign/{{ $campaign->slug }}/donations" class="flex items-center justify-between p-3.5 bg-white border border-gray-100 hover:border-blue-300 hover:shadow-sm rounded-2xl transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-pink-50 text-pink-600 rounded-xl flex items-center justify-center group-hover:bg-pink-100 transition shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-900 text-[13px] mb-0.5">Doa-doa Orang Baik</span>
                            <span class="text-[11px] text-gray-500">{{ $campaign->donations->count() }} Doa dipanjatkan</span>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <!-- Daftar Doa Orang Baik -->
            <div class="mb-10">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Doa-doa Orang Baik</h2>
                    <a href="/campaign/{{ $campaign->slug }}/donations" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua</a>
                </div>
                <div class="space-y-3">
                    @forelse($campaign->donations->take(3) as $donasi)
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <div class="flex items-center gap-2.5 mb-2">
                                <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-[11px]">H</div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-[12px]">Hamba Allah</h4>
                                    <span class="text-[10px] text-gray-500">{{ $donasi->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <p class="text-[12px] text-gray-600 italic">"Semoga donasi ini bermanfaat dan menjadi berkah bagi semuanya. Aamiin."</p>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400 text-xs border border-dashed border-gray-200 rounded-xl bg-gray-50">
                            Belum ada doa. Jadilah yang pertama berdonasi.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Cross-selling Program Lainnya -->
            <div>
                <h2 class="text-lg font-bold text-gray-900 mb-4">Program Kebaikan Lainnya</h2>
                <div class="flex overflow-x-auto gap-4 pb-4 snap-x snap-mandatory hide-scroll-bar -mx-5 px-5">
                    @foreach($otherCampaigns as $other)
                        <a href="/campaign/{{ $other->slug }}" class="shrink-0 w-[220px] snap-start bg-white rounded-xl border border-gray-100 hover:border-blue-300 hover:shadow-md transition-all overflow-hidden flex flex-col">
                            <div class="h-28 bg-gray-200 w-full shrink-0">
                                @if($other->cover_image)
                                    <img src="{{ Storage::url($other->cover_image) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-[10px] text-gray-400 bg-gray-100">No Image</div>
                                @endif
                            </div>
                            <div class="p-3 flex flex-col flex-grow">
                                <h3 class="font-bold text-gray-900 text-[13px] line-clamp-2 mb-2">{{ $other->title }}</h3>
                                <div class="mt-auto">
                                    <div class="w-full bg-gray-100 rounded-full h-1 mb-1.5">
                                        @php $pct = $other->target_amount > 0 ? min(100, ($other->current_amount / $other->target_amount) * 100) : 0; @endphp
                                        <div class="bg-blue-500 h-1 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-[13px] font-bold text-blue-600">Rp {{ number_format($other->current_amount ?? 0, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            
        </div> <!-- Akhir Konten -->
    </div>

    <!-- STICKY BOTTOM BAR (Tengah presisi pakai inset-x-0 mx-auto) -->
    <div x-show="showSticky" 
         style="display: none;"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="fixed bottom-0 inset-x-0 mx-auto w-full max-w-[480px] z-50 bg-white border-t border-gray-200 p-3 flex gap-2.5 shadow-[0_-5px_15px_rgba(0,0,0,0.05)]">
        
        <!-- Tombol Share -->
        <button class="flex-none bg-white border border-gray-200 text-gray-700 font-bold px-4 py-3 rounded-xl flex items-center justify-center hover:bg-gray-50 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
        </button>
        
        <!-- Tombol Donasi -->
        <button class="flex-grow bg-[#E11D48] hover:bg-rose-700 text-white font-bold text-[15px] py-3 rounded-xl shadow-md transition-colors">
            Donasi Sekarang
        </button>
    </div>

</div>