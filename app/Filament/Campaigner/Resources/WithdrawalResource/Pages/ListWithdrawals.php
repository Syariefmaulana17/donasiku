<?php

namespace App\Filament\Campaigner\Resources\WithdrawalResource\Pages;

use App\Filament\Campaigner\Resources\WithdrawalResource;
use App\Filament\Campaigner\Resources\WithdrawalResource\Widgets\WithdrawalStatsWidget; // Memanggil widget
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWithdrawals extends ListRecords
{
    protected static string $resource = WithdrawalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Mengubah teks tombol agar sama dengan desain referensi
            Actions\CreateAction::make()->label('Ajukan pencairan'),
        ];
    }

    // 3. FUNGSI INI YANG MEMBUAT KARTU STATISTIK MUNCUL DI ATAS TABEL
    protected function getHeaderWidgets(): array
    {
        return [
            WithdrawalStatsWidget::class,
        ];
    }
}