<?php

namespace App\Filament\Widgets;

use App\Models\Donation;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class DonationChart extends ChartWidget
{
    // 1. Judul grafik sudah diperbarui (tanpa teks Tahun Ini)
    protected static ?string $heading = 'Perbandingan Tren Donasi';
    
    protected static ?int $sort = 2; 
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Menyiapkan tiga wadah untuk menampung seluruh status data
        $dataSukses = [];
        $dataPending = [];
        $dataGagal = []; 
        $labels = [];
        
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $labels[] = Carbon::create()->month($bulan)->translatedFormat('M');
            
            // Hitung Donasi Sukses
            $dataSukses[] = Donation::where('status', 'success')
                ->whereYear('created_at', date('Y'))
                ->whereMonth('created_at', $bulan)
                ->sum('amount');

            // Hitung Donasi Pending
            $dataPending[] = Donation::where('status', 'pending')
                ->whereYear('created_at', date('Y'))
                ->whereMonth('created_at', $bulan)
                ->sum('amount');
                
            // Hitung Donasi Gagal
            $dataGagal[] = Donation::where('status', 'failed')
                ->whereYear('created_at', date('Y'))
                ->whereMonth('created_at', $bulan)
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Donasi Sukses (Rp)',
                    'data' => $dataSukses,
                    'borderColor' => '#10b981', // Warna Hijau
                    'backgroundColor' => 'transparent',
                    'tension' => 0.4, // 2. Membuat garis menjadi melengkung/halus
                ],
                [
                    'label' => 'Potensi Pending (Rp)',
                    'data' => $dataPending,
                    'borderColor' => '#f59e0b', // Warna Oranye
                    'backgroundColor' => 'transparent',
                    'tension' => 0.4, // Membuat garis menjadi melengkung/halus
                ],
                [
                    'label' => 'Donasi Gagal (Rp)',
                    'data' => $dataGagal,
                    'borderColor' => '#ef4444', // Warna Merah
                    'backgroundColor' => 'transparent',
                    'tension' => 0.4, // Membuat garis menjadi melengkung/halus
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}