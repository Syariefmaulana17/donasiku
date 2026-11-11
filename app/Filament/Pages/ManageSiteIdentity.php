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
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;

class ManageSiteIdentity extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationGroup = 'Pengaturan Sistem';
    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    protected static ?string $navigationLabel = 'Identitas & Warna';
    protected static ?string $title = 'Identitas & Tema Situs';
    protected static string $view = 'filament.pages.manage-homepage'; // Kita pakai view yang sama

    public ?array $data = [];

    public function mount(): void 
    {
        $setting = HomepageSetting::first() ?? HomepageSetting::create();
        $this->form->fill($setting->toArray());
    }

    public function form(Form $form): Form 
    {
        return $form->schema([
            Grid::make(2)->schema([
                Section::make('Identitas Website')->schema([
                    TextInput::make('site_name')
                        ->label('Nama Website / Aplikasi')
                        ->required(),
                    FileUpload::make('brand_logo')
                        ->label('Logo Dasbor (Menggantikan Teks Pojok Kiri Atas)')
                        ->image()
                        ->disk('public')
                        ->directory('site-identity')
                        ->visibility('public'),
                    FileUpload::make('favicon')
                        ->label('Favicon (Ikon Kecil di Tab Browser)')
                        ->image()
                        ->disk('public')
                        ->directory('site-identity')
                        ->visibility('public'),
                ])->columnSpan(1),

                Section::make('Palet Warna (Tone)')->schema([
                    ColorPicker::make('color_primary')->label('Warna Utama (Akan jadi warna utama Dasbor)')->default('#3b82f6'),
                    ColorPicker::make('color_secondary')->label('Warna Sekunder (Aksen Tambahan)')->default('#10b981'),
                    ColorPicker::make('color_background')->label('Warna Latar Belakang Frontend')->default('#f9fafb'),
                    ColorPicker::make('color_button')->label('Warna Tombol (Slider & Post)')->default('#2563eb'),
                    ColorPicker::make('color_accent')->label('Warna Progress Bar & Banner')->default('#f59e0b'),
                ])->columnSpan(1),
            ])
        ])->statePath('data');
    }

    public function save(): void 
    {
        $setting = HomepageSetting::first() ?? HomepageSetting::create();
        $setting->update($this->form->getState());
        Notification::make()->success()->title('Identitas & Warna Berhasil Disimpan!')->send();
        
        // Memuat ulang halaman agar logo dan warna dasbor langsung berubah
        $this->js('window.location.reload()');
    }
}