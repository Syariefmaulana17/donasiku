<?php

namespace App\Filament\Donatur\Resources\DonationResource\Pages;

use App\Filament\Donatur\Resources\DonationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDonation extends EditRecord
{
    protected static string $resource = DonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
