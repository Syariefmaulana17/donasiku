<?php

namespace App\Filament\Donatur\Resources\DonationResource\Widgets;

use Filament\Widgets\Widget;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Livewire\Attributes\Url;

class DonaturDateFilterWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    // Menunjuk ke file tampilan blade di Langkah 2
    protected static string $view = 'filament.donatur.resources.donation-resource.widgets.donatur-date-filter-widget';
    
    protected int | string | array $columnSpan = 'full';

    #[Url]
    public ?string $startDate = null;

    #[Url]
    public ?string $endDate = null;

    public function mount(): void
    {
        if (empty($this->startDate)) {
            $this->startDate = now()->subDays(30)->format('Y-m-d');
        }
        if (empty($this->endDate)) {
            $this->endDate = now()->format('Y-m-d');
        }

        $this->form->fill([
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    DatePicker::make('startDate')
                        ->label('Dari Tanggal')
                        ->required()
                        ->live() // Otomatis refresh saat tanggal dipilih
                        ->afterStateUpdated(fn () => $this->applyFilter()),
                        
                    DatePicker::make('endDate')
                        ->label('Sampai Tanggal')
                        ->required()
                        ->live() // Otomatis refresh saat tanggal dipilih
                        ->afterStateUpdated(fn () => $this->applyFilter()),
                ]),
            ]);
    }

    public function applyFilter()
    {
        $state = $this->form->getState(); 
        $url = request()->url() . '?startDate=' . $state['startDate'] . '&endDate=' . $state['endDate'];
        return redirect()->to($url);
    }
}