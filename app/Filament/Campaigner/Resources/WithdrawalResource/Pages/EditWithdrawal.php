<?php

namespace App\Filament\Campaigner\Resources\WithdrawalResource\Pages;

use App\Filament\Campaigner\Resources\WithdrawalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWithdrawal extends EditRecord
{
    protected static string $resource = WithdrawalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
