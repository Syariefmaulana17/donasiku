<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Donasi - {{ $donation->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-black font-sans p-8 max-w-2xl mx-auto" onload="window.print()">

    <div class="border-2 border-gray-800 p-8 rounded-lg">
        <div class="flex justify-between items-start border-b-2 border-gray-800 pb-6 mb-6">
            <div>
                <h1 class="text-3xl font-black uppercase tracking-widest">Kwitansi</h1>
                <p class="text-gray-600 mt-1 font-medium">Tanda Terima Donasi</p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-bold">DonasiKu</h2>
                <p class="text-sm">www.dqws.dayatumbuh.com</p>
                <p class="text-sm mt-2">ID Trx: #DON-{{ $donation->id }}-{{ $donation->created_at->format('Ymd') }}</p>
                <p class="text-sm">Tanggal: {{ $donation->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>

        <div class="mb-8">
            <p class="text-sm text-gray-600 font-bold mb-1">Telah Diterima Dari:</p>
            <p class="text-xl font-bold">{{ $donation->donor_name }}</p>
        </div>

        <table class="w-full mb-8 text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-gray-800">
                    <th class="py-2 font-bold">Keterangan / Program</th>
                    <th class="py-2 font-bold text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-gray-300">
                    <td class="py-4">{{ $donation->campaign->title ?? 'Program Kebaikan' }}</td>
                    <td class="py-4 text-right font-bold">Rp {{ number_format($donation->amount, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="flex justify-between items-end">
            <div class="text-center">
                <p class="text-sm mb-12">Hormat Kami,</p>
                <p class="font-bold border-t border-gray-400 pt-1">Admin DonasiKu</p>
            </div>
            <div class="text-right bg-gray-100 px-6 py-4 rounded">
                <p class="text-sm font-bold text-gray-600 mb-1">Total Penerimaan</p>
                <p class="text-2xl font-black">Rp {{ number_format($donation->amount, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <p class="text-center text-xs text-gray-400 mt-4">*Semoga Allah membalas kebaikan Anda dengan balasan yang berlipat ganda.</p>

</body>
</html>