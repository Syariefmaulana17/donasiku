<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>{{ $title ?? 'Platform DonasiKu' }}</title>

        <!-- Memuat Tailwind CSS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Memuat gaya bawaan Livewire -->
        @livewireStyles
    </head>
    <body class="antialiased font-sans text-gray-900 bg-gray-50">
        
        <!-- Di sinilah lukisan (komponen) CampaignDetail Anda akan dimuat -->
        {{ $slot }}

        <!-- Memuat script bawaan Livewire -->
        @livewireScripts
    </body>
</html>