<?php

namespace App\Filament\Widgets;

use App\Models\Donation;
use Filament\Widgets\ChartWidget;

class DonationStatusPieChart extends ChartWidget
{
    // Judul Pie Chart
    protected static ?string $heading = 'Rasio Status Donasi';
    
    // Urutan ke-3 (Di bawah Grafik Garis)
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // Menghitung jumlah donasi berdasarkan statusnya
        $success = Donation::where('status', 'success')->count();
        $pending = Donation::where('status', 'pending')->count();
        $failed = Donation::where('status', 'failed')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Transaksi',
                    'data' => [$success, $pending, $failed],
                    // Memberikan warna yang sesuai: Hijau, Oranye, Merah
                    'backgroundColor' => ['#10b981', '#f59e0b', '#ef4444'], 
                ],
            ],
            'labels' => ['Sukses', 'Pending', 'Gagal'],
        ];
    }

    protected function getType(): string
    {
        // Anda bisa mengubah 'pie' menjadi 'doughnut' jika ingin tengahnya bolong seperti donat
        return 'pie'; 
    }
}