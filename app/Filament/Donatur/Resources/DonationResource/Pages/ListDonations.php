<?php

namespace App\Filament\Donatur\Resources\DonationResource\Pages;

use App\Filament\Donatur\Resources\DonationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ListDonations extends ListRecords
{
    protected static string $resource = DonationResource::class;

    // 1. MEMANGGIL WIDGET KE ATAS HALAMAN
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Donatur\Resources\DonationResource\Widgets\DonaturDateFilterWidget::class,
        ];
    }

    // 2. MEMAKSA TABEL MEMBACA TANGGAL DARI URL
    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        $startDate = request('startDate');
        $endDate = request('endDate');

        // Jika URL memiliki tanggal, saring data tabelnya
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        }

        return $query;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}