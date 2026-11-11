<?php

namespace App\Filament\Campaigner\Resources\CampaignResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class CampaignOverviewStats extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        if (! $this->record) return [];

        // Menangkap tanggal langsung dari parameter URL secara aman
        $startDate = request()->query('startDate');
        $endDate = request()->query('endDate');

        $query = $this->record->donations()->where('status', 'success');
        
        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }
        
        $totalDonasi = (clone $query)->sum('amount');
        $totalDonatur = (clone $query)->distinct('donor_email')->count('donor_email');
        $totalTransaksi = (clone $query)->count();
        $dilihat = $this->record->views ?? 0; 

        return [
            Stat::make('Donasi Terkumpul', 'Rp ' . number_format($totalDonasi, 0, ',', '.'))->color('success'),
            Stat::make('Program dilihat', number_format($dilihat, 0, ',', '.') . ' kali'),
            Stat::make('Total Donatur', number_format($totalDonatur, 0, ',', '.')),
            Stat::make('Total Transaksi', number_format($totalTransaksi, 0, ',', '.')),
        ];
    }
}