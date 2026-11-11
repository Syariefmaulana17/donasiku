<?php

namespace App\Filament\Campaigner\Resources\WithdrawalResource\Pages;

use App\Filament\Campaigner\Resources\WithdrawalResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewWithdrawal extends ViewRecord
{
    protected static string $resource = WithdrawalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
