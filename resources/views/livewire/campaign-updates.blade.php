<div class="bg-gray-100 min-h-screen font-sans text-gray-800 antialiased flex justify-center">
    <div class="w-full max-w-[480px] bg-white min-h-screen shadow-2xl relative border-x border-gray-200 pb-10">
        
        <!-- Header Sticky -->
        <div class="sticky top-0 z-50 bg-white border-b border-gray-100 px-4 py-3 flex items-center gap-4 shadow-sm">
            <a href="/campaign/{{ $campaign->slug }}" class="text-gray-600 hover:bg-gray-100 p-1.5 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="font-bold text-gray-800 text-base">Kabar Terbaru</h1>
        </div>

        <div class="p-5">
            @forelse($campaign->updates as $update)
                <div class="mb-6 pb-6 border-b border-gray-100 last:border-0">
                    <span class="inline-block bg-blue-50 text-blue-600 text-[11px] font-bold px-2.5 py-1 rounded-full mb-3">
                        {{ $update->created_at->format('d M Y') }}
                    </span>
                    <h2 class="font-bold text-gray-900 text-[15px] mb-2">{{ $update->title }}</h2>
                    <div class="prose prose-sm text-gray-600 text-[13px] leading-relaxed max-w-none">
                        {!! $update->content !!}
                    </div>
                </div>
            @empty
                <div class="text-center py-10">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"></path></svg>
                    </div>
                    <p class="text-gray-500 text-sm">Belum ada kabar terbaru untuk program ini.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>