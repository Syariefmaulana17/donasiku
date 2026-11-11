<?php

namespace App\Filament\Campaigner\Widgets;

use App\Models\Campaign;
use App\Models\Donation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Widgets\Concerns\InteractsWithPageFilters; // Wajib agar grafik membaca Date Picker di halaman

class CampaignerRaihanChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Statistik Raihan & Transaksi';
    protected static ?int $sort = 2; 
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $userId = Auth::id();
        $campaignIds = Campaign::where('user_id', $userId)->pluck('id');
        
        // MURNI HANYA MEMBACA DATE PICKER
        $startDateFilter = $this->filters['startDate'] ?? null;
        $endDateFilter = $this->filters['endDate'] ?? null;

        if ($startDateFilter && $endDateFilter) {
            $startDate = Carbon::parse($startDateFilter);
            $endDate = Carbon::parse($endDateFilter);
        } else {
            // Tampilan bawaan jika Date Picker belum diisi pengguna
            $startDate = Carbon::today()->subDays(6);
            $endDate = Carbon::today();
        }

        // Mencegah error jika salah pilih urutan tanggal
        if ($startDate->greaterThan($endDate)) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        $period = CarbonPeriod::create($startDate, $endDate);
        
        $dataRaihan = [];
        $dataTransaksi = [];
        $labels = [];
        
        foreach ($period as $date) {
            $labels[] = $date->translatedFormat('d M'); 
            
            $dataRaihan[] = Donation::whereIn('campaign_id', $campaignIds)
                ->where('status', 'success')
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->sum('amount');
                
            $dataTransaksi[] = Donation::whereIn('campaign_id', $campaignIds)
                ->where('status', 'success')
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Raihan (Rp)',
                    'data' => $dataRaihan,
                    'backgroundColor' => '#3b82f6',
                    'type' => 'bar',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Jumlah Transaksi',
                    'data' => $dataTransaksi,
                    'backgroundColor' => '#fb923c',
                    'type' => 'bar',
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'grid' => ['drawOnChartArea' => false],
                ],
            ],
        ];
    }
}