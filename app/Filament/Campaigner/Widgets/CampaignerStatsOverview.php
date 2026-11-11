<?php

namespace App\Filament\Campaigner\Widgets;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Withdrawal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CampaignerStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $userId = Auth::id();
        $campaignIds = Campaign::where('user_id', $userId)->pluck('id');
        $hariIni = Carbon::today();

        // Data Keseluruhan
        $totalRaihan = Donation::whereIn('campaign_id', $campaignIds)->where('status', 'success')->sum('amount');
        $totalPencairan = Withdrawal::whereIn('campaign_id', $campaignIds)->where('status', 'completed')->sum('amount');
        
        $biayaAdmin = $totalRaihan * 0.10;
        $saldoBersih = $totalRaihan - $biayaAdmin - $totalPencairan;
        $saldoBersih = $saldoBersih > 0 ? $saldoBersih : 0;

        $programBerjalan = Campaign::where('user_id', $userId)->where('status', 'active')->count();
        $belumDipublikasi = Campaign::where('user_id', $userId)->where('status', 'pending')->count();
        $jumlahProgram = Campaign::where('user_id', $userId)->count();
        $programSelesai = Campaign::where('user_id', $userId)->where('status', 'completed')->count();

        // Data Hari Ini
        $donasiHariIni = Donation::whereIn('campaign_id', $campaignIds)->where('status', 'success')->whereDate('created_at', $hariIni)->sum('amount');
        $donaturHariIni = Donation::whereIn('campaign_id', $campaignIds)->where('status', 'success')->whereDate('created_at', $hariIni)->count();

        return [
            Stat::make('Raihan Keseluruhan', 'Rp ' . number_format($totalRaihan, 0, ',', '.'))->color('primary'),
            Stat::make('Program Berjalan', $programBerjalan)->color('info'),
            Stat::make('Program Selesai', $programSelesai)->color('success'),
            Stat::make('Jumlah Program', $programBerjalan . ' / ' . $jumlahProgram)->color('gray'),

            Stat::make('Saldo Raihan (Bersih)', 'Rp ' . number_format($saldoBersih, 0, ',', '.'))->color('success'),
            Stat::make('Donasi Hari Ini', 'Rp ' . number_format($donasiHariIni, 0, ',', '.'))->color('warning'),
            Stat::make('Donatur Hari Ini', $donaturHariIni . ' Orang')->color('warning'),
            Stat::make('Belum Dipublikasi', $belumDipublikasi)->color('danger'),
        ];
    }
}