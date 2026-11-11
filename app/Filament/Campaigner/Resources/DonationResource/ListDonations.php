<?php

namespace App\Filament\Campaigner\Resources\DonationResource\Pages;

use App\Filament\Campaigner\Resources\DonationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDonations extends ListRecords
{
    // 1. Menghubungkan halaman ini dengan file DonationResource yang kita buat tadi
    protected static string $resource = DonationResource::class;

    // 2. Mengatur tombol di kanan atas tabel
    protected function getHeaderActions(): array
    {
        return [
            // Sengaja dikosongkan agar rapi dan tidak ada tombol "Create" atau "Buat Baru"
            // karena Campaigner hanya boleh melihat daftar donatur.
        ];
    }
}