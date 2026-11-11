<?php

namespace App\Filament\Campaigner\Resources\CampaignResource\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Livewire\Attributes\Url;

class CampaignDonationChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Donasi & Transaksi';
    public ?Model $record = null;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '300px';

    #[Url]
    public ?string $startDate = null;

    #[Url]
    public ?string $endDate = null;

    protected function getData(): array
    {
        $start = $this->startDate 
            ? Carbon::parse($this->startDate)->startOfDay() 
            : now()->subDays(7)->startOfDay();
            
        $end = $this->endDate 
            ? Carbon::parse($this->endDate)->endOfDay() 
            : now()->endOfDay();

        $campaignId = $this->record ? $this->record->id : request()->route('record');

        $donations = \App\Models\Donation::where('campaign_id', $campaignId)
            ->where('status', 'success') 
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, sum(amount) as aggregate, count(id) as transaction_count')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $dataAmount = [];
        $dataTransaction = [];
        
        $currentDate = $start->copy();
        
        while ($currentDate->lte($end)) {
            $dateString = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->translatedFormat('d M');
            
            if ($donations->has($dateString)) {
                $dataAmount[] = (float) $donations->get($dateString)->aggregate;
                $dataTransaction[] = (int) $donations->get($dateString)->transaction_count;
            } else {
                $dataAmount[] = 0;
                $dataTransaction[] = 0;
            }
            
            $currentDate->addDay();
        }

        return [
            'datasets' => [
                [
                    'type' => 'line', // Garis utama
                    'label' => 'Total Raihan (Rp)',
                    'data' => $dataAmount,
                    'borderColor' => '#3b82f6', 
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4, // Membuat garis melengkung halus
                    'yAxisID' => 'y', 
                ],
                [
                    'type' => 'bar', // Ubah menjadi batang agar tidak bertabrakan dengan garis biru
                    'label' => 'Jumlah Transaksi',
                    'data' => $dataTransaction,
                    'backgroundColor' => '#f97316', 
                    'borderRadius' => 4, // Efek batang tumpul (modern)
                    'yAxisID' => 'y1', 
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Base chart tetap line
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
                    'suggestedMax' => 5, // Agar 1 transaksi tidak menyentuh atap grafik
                    'ticks' => [
                        'stepSize' => 1, // Memaksa sumbu Y kanan menggunakan angka bulat (1, 2, 3)
                    ],
                ],
            ],
        ];
    }
}