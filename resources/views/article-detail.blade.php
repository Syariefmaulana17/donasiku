@php
    $homeSetting = \App\Models\HomepageSetting::first();
    $siteName = $homeSetting->site_name ?? 'DonasiKu';
    $favicon = !empty($homeSetting->favicon) ? Storage::url($homeSetting->favicon) : null;
    $navbarColor = $homeSetting->navbar_color ?? '#ffffff';
    $colorPrimary = $homeSetting->color_primary ?? '#2563eb';
    $colorBtn = $homeSetting->color_button ?? '#2563eb';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $article->meta_title ?: $article->title }} - {{ $siteName }}</title>
    <meta name="description" content="{{ $article->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($article->content), 155) }}">
    @if($favicon)
        <link rel="icon" href="{{ $favicon }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: { brand: { primary: '{{ $colorPrimary }}', btn: '{{ $colorBtn }}' } }
                }
            }
        }
    </script>
    <style>
        .prose h2 { font-size: 1.5rem; font-weight: 800; margin-top: 1.5rem; margin-bottom: 0.75rem; color: #0f172a; }
        .prose h3 { font-size: 1.25rem; font-weight: 700; margin-top: 1.25rem; margin-bottom: 0.5rem; color: #1e293b; }
        .prose p { margin-bottom: 1.1rem; line-height: 1.85; color: #334155; }
        .prose ul { list-style-type: disc; margin-left: 1.5rem; margin-bottom: 1.1rem; }
        .prose ol { list-style-type: decimal; margin-left: 1.5rem; margin-bottom: 1.1rem; }
        .prose img { border-radius: 1rem; margin: 1.5rem 0; width: 100%; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">
    <!-- Navbar -->
    <nav class="sticky top-0 z-50 border-b border-slate-200/80 backdrop-blur-md" style="background-color: {{ $navbarColor }}f2">
        <div class="max-w-7xl mx-auto px-4 md:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 font-extrabold text-xl text-slate-900">
                @if($homeSetting && $homeSetting->logo)
                    <img src="{{ Storage::url($homeSetting->logo) }}" class="h-9 object-contain" alt="{{ $siteName }}">
                @else
                    <span>{{ $siteName }}<span class="text-brand-primary">.</span></span>
                @endif
            </a>
            <a href="/" class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-brand-primary transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Beranda
            </a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 md:px-8 py-8 md:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Konten Artikel (8 Kolom) -->
            <article class="lg:col-span-8 bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                @if($article->cover_image)
                    <div class="aspect-video w-full bg-slate-100 overflow-hidden">
                        <img src="{{ Storage::url($article->cover_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                    </div>
                @endif
                <div class="p-6 md:p-10">
                    <div class="flex items-center gap-3 mb-4">
                        @if($article->category)
                            <span class="px-3 py-1 rounded-full bg-blue-50 text-brand-primary text-xs font-bold">{{ $article->category->name }}</span>
                        @endif
                        <span class="text-xs text-slate-400 font-medium">{{ $article->created_at->translatedFormat('d F Y') }}</span>
                    </div>
                    <h1 class="text-2xl md:text-4xl font-extrabold text-slate-900 leading-tight mb-6">{{ $article->title }}</h1>
                    <div class="prose max-w-none">
                        {!! $article->content !!}
                    </div>
                </div>
            </article>

            <!-- Sidebar Program Rekomendasi (4 Kolom Sticky) -->
            <aside class="lg:col-span-4 space-y-6">
                <div class="sticky top-24 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                    <h3 class="font-extrabold text-lg text-slate-900 mb-4">Mari Bantu Mereka</h3>
                    <div class="space-y-4">
                        @foreach($recommendedCampaigns as $camp)
                            <a href="/campaign/{{ $camp->slug }}" class="flex gap-3.5 group items-center p-2 -mx-2 rounded-2xl hover:bg-slate-50 transition">
                                <div class="w-20 h-16 rounded-xl bg-slate-100 overflow-hidden shrink-0">
                                    @if($camp->cover_image)
                                        <img src="{{ Storage::url($camp->cover_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-bold text-slate-800 text-xs line-clamp-2 group-hover:text-brand-primary transition">{{ $camp->title }}</h4>
                                    <p class="text-xs font-extrabold text-brand-primary mt-1">Rp {{ number_format($camp->donations_sum_amount ?? $camp->current_amount ?? 0, 0, ',', '.') }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    </main>
</body>
</html>

