<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Persetujuan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100" style="background-image: radial-gradient(circle at center, #d1fae5 0%, #f3f4f6 100%);">
    <div class="p-8 text-center bg-white rounded-3xl shadow-2xl max-w-md border border-gray-100">
        <!-- Icon Jam / Menunggu -->
        <svg class="w-24 h-24 mx-auto text-yellow-500 mb-6 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>

        <h2 class="text-3xl font-bold text-gray-800 mb-4">Pendaftaran Berhasil!</h2>
        <p class="text-gray-600 mb-8 leading-relaxed">Akun Anda sedang ditinjau. Memerlukan persetujuan dari Super Admin sebelum Anda dapat mengakses Dasbor. Silakan hubungi admin atau coba login kembali nanti.</p>

        <a href="/admin/login" class="inline-block px-8 py-3 bg-green-600 text-white font-semibold rounded-xl shadow-lg hover:bg-green-700 hover:-translate-y-1 transition-all duration-300">
            Kembali ke Login
        </a>
    </div>
</body>
</html>