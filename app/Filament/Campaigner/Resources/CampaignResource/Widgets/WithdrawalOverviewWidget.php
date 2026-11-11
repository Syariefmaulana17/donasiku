<?php

namespace App\Filament\Campaigner\Resources\CampaignResource\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class WithdrawalOverviewWidget extends Widget
{
    // Mengarahkan ke file tampilan (Blade) yang akan kita buat di Langkah 3
    protected static string $view = 'filament.campaigner.resources.campaign-resource.widgets.withdrawal-overview-widget';
    protected int | string | array $columnSpan = 'full';

    public ?Model $record = null;

    protected function getViewData(): array
    {
        // 1. Total Raihan (Hanya donasi sukses)
        $totalDonasi = $this->record->donations()->where('status', 'success')->sum('amount');
        
        // 2. Potongan Biaya Platform (10%)
        $biayaPlatform = $totalDonasi * 0.10;
        
        // 3. Dana yang sudah dicairkan (Status completed atau approved)
        $totalDicairkan = $this->record->withdrawals()->whereIn('status', ['completed', 'approved'])->sum('amount');
        
        // 4. Sisa Saldo Tersedia
        $sisaSaldo = $totalDonasi - $biayaPlatform - $totalDicairkan;

        return [
            'totalDonasi' => $totalDonasi,
            'biayaPlatform' => $biayaPlatform,
            'totalDicairkan' => $totalDicairkan,
            'sisaSaldo' => max(0, $sisaSaldo), // Mencegah angka minus
        ];
    }
}