<?php

namespace App\Filament\Campaigner\Resources\WithdrawalResource\Widgets;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Withdrawal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class WithdrawalStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();
        $campaignIds = Campaign::where('user_id', $userId)->pluck('id');

        // Menghitung data keuangan
        $totalRaihan = Donation::whereIn('campaign_id', $campaignIds)->where('status', 'success')->sum('amount');
        $totalDicairkan = Withdrawal::whereIn('campaign_id', $campaignIds)->where('status', 'completed')->sum('amount');
        
        // Asumsi biaya admin 10% seperti perhitungan kita di dasbor sebelumnya
        $biayaAdmin = $totalRaihan * 0.10;
        $saldoBersih = $totalRaihan - $biayaAdmin - $totalDicairkan;
        $saldoBersih = $saldoBersih > 0 ? $saldoBersih : 0;

        return [
            Stat::make('Saldo seluruh program', 'Rp ' . number_format($saldoBersih, 0, ',', '.'))
                ->color('success')
                ->extraAttributes(['class' => 'bg-green-50']), // Memberikan efek background kehijauan sedikit
                
            Stat::make('Total raihan', 'Rp ' . number_format($totalRaihan, 0, ',', '.')),
            
            Stat::make('Total sudah dicairkan', 'Rp ' . number_format($totalDicairkan, 0, ',', '.')),
        ];
    }
}