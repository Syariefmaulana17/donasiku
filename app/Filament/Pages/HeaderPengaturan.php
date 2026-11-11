<?php

namespace App\Filament\Pages;

use App\Models\HomepageSetting;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Notifications\Notification;

// KITA MENGGUNAKAN NAMA CLASS BARU
class HeaderPengaturan extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationGroup = 'Konfigurasi Tampilan';
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    protected static ?int $navigationSort = 1;
    
    // NAMA MENU & JUDUL YANG PASTI BERSIH
    protected static ?string $navigationLabel = 'Header & Navbar';
    protected static ?string $title = 'Header & Navbar';
    
    // URL BARU AGAR SISTEM MEMBUAT JALUR BARU
    protected static ?string $slug = 'header-pengaturan';

    protected static string $view = 'filament.pages.manage-homepage';

    public ?array $data = [];

    public function mount(): void 
    {
        $setting = HomepageSetting::first() ?? HomepageSetting::create();
        $this->form->fill($setting->toArray());
    }

    public function form(Form $form): Form 
    {
        return $form->schema([
            Section::make('Pengaturan Header (Bagian Atas Web)')->schema([
                FileUpload::make('logo')
                    ->label('Logo Website')
                    ->image()
                    ->directory('web-assets'),
                ColorPicker::make('navbar_color')
                    ->label('Warna Background Navbar')
                    ->default('#ffffff')
                    ->required(),
                Repeater::make('navbar_menu')
                    ->label('Menu Navbar')
                    ->schema([
                        TextInput::make('label')->label('Nama Menu')->required(),
                        TextInput::make('url')->label('Link URL')->required(),
                    ])->columns(2),
            ])
        ])->statePath('data');
    }

    public function save(): void 
    {
        $setting = HomepageSetting::first() ?? HomepageSetting::create();
        $setting->update($this->form->getState());
        Notification::make()->success()->title('Header Berhasil Disimpan!')->send();
    }
}