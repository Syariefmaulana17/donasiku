<?php

namespace App\Filament\Campaigner\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('startDate')
                    ->label('Dari Tanggal')
                    ->maxDate(now()),
                DatePicker::make('endDate')
                    ->label('Sampai Tanggal')
                    ->maxDate(now()),
            ])
            // Membagi layar menjadi 4 kolom agar input form tidak memanjang penuh
            ->columns(4); 
    }
}