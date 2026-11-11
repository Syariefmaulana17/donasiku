<?php

namespace App\Filament\Campaigner\Resources\CampaignResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class CampaignDetailedStats extends BaseWidget
{
    public ?Model $record = null;

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        if (! $this->record) return [];

        $startDate = request()->query('startDate');
        $endDate = request()->query('endDate');

        $baseQuery = $this->record->donations();
        
        if (!empty($startDate) && !empty($endDate)) {
            $baseQuery->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $pengunjung = $this->record->views ?? 0;
        $mengisiForm = (clone $baseQuery)->count(); 
        $totalTransaksi = (clone $baseQuery)->where('status', 'success')->count();
        $donasiPending = (clone $baseQuery)->where('status', 'pending')->count();
        
        $target = $this->record->target ?? 0;
        $terkumpul = (clone $baseQuery)->where('status', 'success')->sum('amount');
        $persentase = $target > 0 ? floor(($terkumpul / $target) * 100) : 100;

        return [
            Stat::make('Pengunjung', number_format($pengunjung, 0, ',', '.'))->icon('heroicon-o-users')->color('warning'),
            Stat::make('Klik Donasi', '0')->icon('heroicon-o-cursor-arrow-rays')->color('warning'),
            Stat::make('Mengisi Form', number_format($mengisiForm, 0, ',', '.'))->icon('heroicon-o-document-text')->color('warning'),
            Stat::make('Total Transaksi', number_format($totalTransaksi, 0, ',', '.'))->icon('heroicon-o-chart-pie')->color('warning'),
            Stat::make('Persentase', $persentase . '%')->icon('heroicon-o-chart-bar')->color('warning'),
            Stat::make('Donasi Pending', number_format($donasiPending, 0, ',', '.'))->icon('heroicon-o-clock')->color('warning'),
        ];
    }
}