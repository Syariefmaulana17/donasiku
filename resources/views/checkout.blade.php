<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Donasi - {{ $campaign->title }}</title>
    <!-- ================= SISTEM CCTV (TRACKING) ================= -->
    @php
        $setting = \App\Models\WebSetting::first();
    @endphp

    @if($setting && $setting->gtm_id)
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ $setting->gtm_id }}');</script>
        <!-- End Google Tag Manager -->
    @endif

    @if($setting && $setting->pixel_id)
        <!-- Meta Pixel Code -->
        <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $setting->pixel_id }}');
        fbq('track', 'PageView');
        </script>
        <!-- End Meta Pixel Code -->
    @endif
    <!-- ================= END CCTV ================= -->
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans p-4 md:p-8">
    
    <div class="max-w-xl mx-auto bg-white p-6 md:p-8 rounded-lg shadow-md border border-gray-200 mt-10">
        <h2 class="text-2xl font-bold mb-6 text-blue-600 text-center">Formulir Setoran Donasi</h2>

        <!-- Info Program yang Dipilih -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="text-lg font-bold text-gray-800">{{ $campaign->title }}</h3>
            <p class="text-sm text-gray-600 mt-1">Target Dana: Rp {{ number_format($campaign->target_amount, 0, ',', '.') }}</p>
        </div>

        <!-- Form Input Data -->
        <form action="{{ url('/donasi/' . $campaign->id) }}" method="POST" enctype="multipart/form-data">
            @csrf <!-- Wajib ada! Ini Kunci Keamanan Laravel -->

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Nama Anda (Wajib)</label>
                <input type="text" name="donor_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Orang Baik">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Email Anda (Opsional)</label>
                <input type="email" name="donor_email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: orangbaik@email.com">
            </div>

            <div class="mb-8">
                <label class="block text-gray-700 font-bold mb-2">Nominal Donasi (Rp) (Wajib)</label>
                <input type="number" name="amount" required min="1000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: 50000">
                <p class="text-xs text-gray-500 mt-1">*Minimal donasi Rp 1.000</p>
            </div>

            <div class="mb-8">
             <label class="block text-gray-700 font-bold mb-2">Upload Bukti Transfer (Opsional)</label>
             <input type="file" name="proof_of_payment" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
             <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Boleh dikosongkan jika pakai sistem otomatis nantinya.</p>
             </div>

            <div class="flex justify-between items-center">
                <a href="/" class="text-gray-500 hover:text-gray-800 font-bold text-sm">⬅ Batal & Kembali</a>
                <button type="submit" class="bg-blue-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-700 transition">
                    Lanjut Bayar
                </button>
            </div>
        </form>
    </div>

</body>
</html>