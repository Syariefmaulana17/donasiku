<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil - DonasiKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased py-10 px-4">

    <!-- Kotak Utama -->
    <div class="max-w-md w-full mx-auto bg-white rounded-2xl shadow-lg p-8 text-center border border-gray-100">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-800 mb-2">Alhamdulillah, Donasi Berhasil!</h1>
        <p class="text-gray-500 text-[14px] mb-8">Terima kasih orang baik, kebaikan Anda telah kami terima dan akan segera disalurkan.</p>

        <div class="bg-gray-50 rounded-xl p-5 text-left space-y-4 border border-gray-100">
            <div>
                <p class="text-[12px] font-bold text-gray-400 uppercase tracking-wider">Nama Donatur</p>
                <p class="text-[15px] font-bold text-gray-800">{{ $donation->donor_name }}</p>
            </div>
            <div>
                <p class="text-[12px] font-bold text-gray-400 uppercase tracking-wider">Program Pilihan</p>
                <p class="text-[15px] font-bold text-gray-800">{{ $donation->campaign->title ?? 'Program Kebaikan' }}</p>
            </div>
            <div>
                <p class="text-[12px] font-bold text-gray-400 uppercase tracking-wider">Nominal Donasi</p>
                <p class="text-[18px] font-bold text-green-600">Rp {{ number_format($donation->amount, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Tombol Aksi (Dibagi 2) -->
        <div class="mt-8 flex gap-3">
            <a href="/" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3.5 rounded-xl transition text-[13px]">
                Ke Beranda
            </a>
            <a href="/payment/receipt/{{ $donation->id }}" target="_blank" class="w-1/2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl transition text-[13px] flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Kwitansi
            </a>
        </div>
    </div>

    <!-- Upselling Program Lain -->
    <div class="max-w-md w-full mx-auto mt-10">
        <h3 class="font-bold text-gray-900 text-[15px] mb-4 text-center">Sekalian sedekah di program lain?</h3>
        <div class="space-y-3">
            @foreach($otherCampaigns as $other)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 flex gap-4 items-center">
                <div class="w-20 h-20 bg-gray-200 rounded-lg shrink-0 overflow-hidden">
                    @if($other->cover_image)
                        <img src="{{ Storage::url($other->cover_image) }}" class="w-full h-full object-cover">
                    @endif
                </div>
                <div class="flex-grow">
                    <h4 class="font-bold text-gray-800 text-[13px] line-clamp-2 leading-tight">{{ $other->title }}</h4>
                    <p class="text-[11px] text-gray-500 mt-1">Terkumpul: Rp {{ number_format($other->current_amount ?? 0, 0, ',', '.') }}</p>
                    <a href="/campaign/{{ $other->slug }}/checkout" class="mt-2 inline-block text-[11px] bg-blue-50 text-blue-600 font-bold px-3 py-1.5 rounded-lg hover:bg-blue-100 transition">Donasi Lagi</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</body>
</html>