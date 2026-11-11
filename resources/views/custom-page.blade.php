<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }} - DonasiKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Tambahan CSS untuk merapikan hasil dari Rich Editor (seperti list bullet, heading, dll) -->
    <style>
        .prose h2 { font-size: 1.5rem; font-weight: bold; margin-top: 1.5rem; margin-bottom: 1rem; }
        .prose h3 { font-size: 1.25rem; font-weight: bold; margin-top: 1.5rem; margin-bottom: 1rem; }
        .prose p { margin-bottom: 1rem; line-height: 1.75; }
        .prose ul { list-style-type: disc; margin-left: 1.5rem; margin-bottom: 1rem; }
        .prose ol { list-style-type: decimal; margin-left: 1.5rem; margin-bottom: 1rem; }
        .prose a { color: #2563eb; text-decoration: underline; }
        .prose blockquote { border-left-width: 4px; border-color: #e5e7eb; padding-left: 1rem; font-style: italic; color: #4b5563; }
        .prose img { border-radius: 0.5rem; margin-top: 1rem; margin-bottom: 1rem; max-width: 100%; height: auto; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">

    <!-- Navbar Sederhana Terintegrasi Warna -->
    <nav class="shadow-sm border-b border-gray-100 p-4 sticky top-0 z-50" style="background-color: {{ $navbarColor }}">
        <div class="container mx-auto flex justify-between items-center max-w-4xl">
            <a href="/" class="text-2xl font-black text-blue-600 tracking-tight">DonasiKu <span class="text-blue-500">💙</span></a>
            <a href="/" class="text-gray-600 hover:text-blue-600 font-bold text-sm transition">⬅ Kembali ke Beranda</a>
        </div>
    </nav>

    <!-- Isi Halaman Dinamis -->
    <main class="container mx-auto max-w-4xl py-10 px-4 md:px-8 min-h-screen">
        <div class="bg-white p-6 md:p-10 rounded-2xl shadow-sm border border-gray-100">
            <h1 class="text-3xl md:text-4xl font-black text-gray-900 mb-8 border-b-2 border-gray-100 pb-4">{{ $page->title }}</h1>

            <!-- Konten dari Database -->
            <div class="prose max-w-none">
                {!! $page->content !!}
            </div>
        </div>
    </main>

</body>
</html>