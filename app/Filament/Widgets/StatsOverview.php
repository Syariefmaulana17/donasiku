<?php

namespace App\Filament\Widgets;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Withdrawal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Schema;

class StatsOverview extends BaseWidget
{
    // Urutan ke-1 (Tampil paling atas di atas grafik)
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 1. Hitung Dana Terkumpul (Sukses)
        $totalDonasiSukses = Donation::where('status', 'success')->sum('amount');
        
        // 2. Hitung Potensi Dana (Pending)
        $totalDonasiPending = Donation::where('status', 'pending')->sum('amount');
        
        // 3. Hitung Jumlah Transaksi Berhasil (Banyaknya Donatur)
        $totalTransaksi = Donation::where('status', 'success')->count();

        // 4. Hitung Program (Campaign) yang sedang aktif
        $totalCampaign = Campaign::where('status', 'active')->count();

        // 5. Hitung Request Pencairan Dana yang masih pending
        $pendingWithdrawal = Withdrawal::where('status', 'pending')->count();
        
        // 6. Hitung Total Artikel Berita yang ditayangkan
        // (Kita cek dulu apakah tabelnya ada agar tidak error saat instalasi baru)
        $totalArtikel = 0;
        if (Schema::hasTable('articles')) {
            $totalArtikel = \App\Models\Article::where('is_published', true)->count();
        }

        return [
            // Kartu 1
            Stat::make('Dana Terkumpul (Sukses)', 'Rp ' . number_format($totalDonasiSukses, 0, ',', '.'))
                ->description('Total dana yang sudah masuk')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Grafik mini hiasan

            // Kartu 2
            Stat::make('Potensi Dana (Pending)', 'Rp ' . number_format($totalDonasiPending, 0, ',', '.'))
                ->description('Menunggu pembayaran donatur')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([3, 5, 2, 8, 14, 10, 12]),

            // Kartu 3
            Stat::make('Transaksi Berhasil', $totalTransaksi . ' Transaksi')
                ->description('Jumlah donatur yang berpartisipasi')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            // Kartu 4
            Stat::make('Program Donasi Aktif', $totalCampaign . ' Program')
                ->description('Campaign yang sedang berjalan saat ini')
                ->descriptionIcon('heroicon-m-heart')
                ->color('success'),

            // Kartu 5
            Stat::make('Request Pencairan', $pendingWithdrawal . ' Menunggu')
                ->description('Pencairan dana yang perlu diproses')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($pendingWithdrawal > 0 ? 'danger' : 'gray'),
                
            // Kartu 6
            Stat::make('Artikel Berita', $totalArtikel . ' Tayang')
                ->description('Konten edukasi di website')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('primary'),
        ];
    }
}