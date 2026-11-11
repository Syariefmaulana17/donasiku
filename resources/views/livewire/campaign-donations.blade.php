<div class="bg-gray-100 min-h-screen font-sans text-gray-800 antialiased flex justify-center">
    <div class="w-full max-w-[480px] bg-white min-h-screen shadow-2xl relative border-x border-gray-200 pb-10">
        
        <!-- Header Sticky -->
        <div class="sticky top-0 z-50 bg-white border-b border-gray-100 px-4 py-3 flex items-center gap-4 shadow-sm">
            <a href="/campaign/{{ $campaign->slug }}" class="text-gray-600 hover:bg-gray-100 p-1.5 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="font-bold text-gray-800 text-base">Donatur & Doa</h1>
        </div>

        <div class="p-5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="text-3xl font-bold text-blue-600">{{ $campaign->donations->count() }}</div>
                <div class="text-[13px] text-gray-600 leading-tight font-medium">Orang baik sudah<br>berdonasi & berdoa</div>
            </div>
        </div>

        <div class="p-5 space-y-4">
            @forelse($campaign->donations as $donasi)
                <div class="p-4 bg-white border border-gray-100 rounded-xl shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-sm shrink-0">H</div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-[13px]">Hamba Allah</h4>
                            <span class="text-[11px] text-gray-400">{{ $donasi->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="text-[13px] font-bold text-gray-800 mb-2">Berdonasi <span class="text-blue-600">Rp {{ number_format($donasi->amount ?? 0, 0, ',', '.') }}</span></div>
                    
                    <!-- Box Doa -->
                    <p class="text-[12px] text-gray-600 italic bg-gray-50 p-3 rounded-lg border border-gray-100">"Semoga donasi ini bermanfaat dan menjadi berkah bagi semuanya. Aamiin."</p>
                    
                    <!-- Fitur Aamiin -->
                    <div class="flex items-center gap-6 mt-3 pt-3 border-t border-gray-50">
                        <button class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-red-500 transition font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            Aamiin (0)
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-10">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg>
                    </div>
                    <p class="text-gray-500 text-sm">Belum ada donatur. Jadilah orang baik pertama!</p>
                </div>
            @endforelse
        </div>

    </div>
</div>