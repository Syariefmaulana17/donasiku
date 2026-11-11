<div class="w-full">
    <!-- CSS Tambahan untuk menyembunyikan scrollbar bawaan browser -->
    <style>
        .hide-scroll-bar { -ms-overflow-style: none; scrollbar-width: none; }
        .hide-scroll-bar::-webkit-scrollbar { display: none; }
    </style>

    <!-- SECTION: CAROUSEL CAMPAIGN -->
    <div x-data="{
            scrollNextCamp() { $refs.campaignSlider.scrollBy({left: 320, behavior: 'smooth'}) },
            scrollPrevCamp() { $refs.campaignSlider.scrollBy({left: -320, behavior: 'smooth'}) }
        }" class="mt-8 md:mt-12 px-4 md:px-8 relative max-w-7xl mx-auto">
        
        <!-- Judul Dinamis -->
        <div class="flex justify-between items-end mb-4 md:mb-6">
            <div>
                <h2 class="text-xl md:text-3xl font-bold text-gray-800">{{ $title }}</h2>
                @if($subtitle)
                    <p class="text-xs md:text-base text-gray-500 mt-1">{{ $subtitle }}</p>
                @endif
            </div>
            <!-- Tombol Navigasi Desktop -->
            <div class="hidden md:flex gap-3">
                <button @click="scrollPrevCamp()" class="bg-white border border-gray-200 hover:bg-gray-100 hover:text-blue-600 text-gray-600 rounded-full p-2.5 shadow-sm transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button @click="scrollNextCamp()" class="bg-white border border-gray-200 hover:bg-gray-100 hover:text-blue-600 text-gray-600 rounded-full p-2.5 shadow-sm transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>

        <!-- Wadah Kartu -->
        <div x-ref="campaignSlider" class="flex overflow-x-auto gap-3 md:gap-6 pb-6 snap-x snap-mandatory hide-scroll-bar">
            @forelse($campaigns as $campaign)
            <a href="/campaign/{{ $campaign->slug }}" class="shrink-0 w-[45vw] md:w-[31%] snap-start bg-white rounded-xl md:rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col group cursor-pointer block">
                
                <div class="relative h-28 md:h-48 overflow-hidden">
                    @if($campaign->cover_image)
                        <img src="{{ Storage::url($campaign->cover_image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-in-out" alt="{{ $campaign->title }}">
                    @else
                        <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400 text-xs">No Image</div>
                    @endif
                </div>

                <div class="p-3 md:p-5 flex flex-col flex-grow">
                    <div class="flex items-center gap-1.5 md:gap-2 mb-2 md:mb-3">
                        <div class="w-4 h-4 md:w-6 md:h-6 bg-blue-100 rounded-full flex items-center justify-center overflow-hidden text-blue-600">
                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                        </div>
                        <span class="text-[10px] md:text-xs text-gray-600 font-medium line-clamp-1">{{ $campaign->user->name ?? 'Anonim' }}</span>
                        <svg class="w-3 h-3 md:w-4 md:h-4 text-blue-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    </div>
                    
                    <h3 class="font-bold text-gray-800 text-[13px] md:text-lg line-clamp-2 mb-2 md:mb-4 group-hover:text-blue-600 transition-colors leading-snug">{{ $campaign->title }}</h3>

                    <div class="mt-auto">
                        <div class="flex flex-col md:flex-row md:justify-between md:items-end mb-1.5 md:mb-2">
                            <div>
                                <span class="text-[10px] md:text-xs text-gray-500 block">Terkumpul</span>
                                <span class="text-[13px] md:text-base font-bold text-blue-600">Rp {{ number_format($campaign->current_amount ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        @php
                            $percent = $campaign->target_amount > 0 ? min(100, ($campaign->current_amount / $campaign->target_amount) * 100) : 0;
                        @endphp
                        
                        <div class="w-full bg-gray-200 rounded-full h-1.5 md:h-2">
                            <div class="bg-blue-500 h-1.5 md:h-2 rounded-full" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="text-gray-500 py-8 w-full text-center border-2 border-dashed border-gray-300 rounded-2xl">
                Belum ada program untuk kategori ini.
            </div>
            @endforelse
        </div>
    </div>
</div>