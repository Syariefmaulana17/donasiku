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

class ManageHeader extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationGroup = 'Konfigurasi Tampilan'; // INI YANG MEMBUAT DROPDOWN
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    protected static ?string $title = '1. Header & Navbar';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.manage-homepage'; // Kita gunakan view yang sama

    public ?array $data = [];

    public function mount(): void {
        $this->form->fill(HomepageSetting::first()->toArray());
    }

    public function form(Form $form): Form {
        return $form->schema([
            Section::make('Pengaturan Header (Bagian Atas Web)')->schema([
                FileUpload::make('logo')->label('Logo Website')->image()->directory('web-assets'),
                ColorPicker::make('navbar_color')->label('Warna Background Navbar')->default('#ffffff')->required(),
                Repeater::make('navbar_menu')->label('Menu Navbar')->schema([
                    TextInput::make('label')->label('Nama Menu')->required(),
                    TextInput::make('url')->label('Link URL')->required(),
                ])->columns(2),
            ])
        ])->statePath('data');
    }

    public function save(): void {
        HomepageSetting::first()->update($this->form->getState());
        Notification::make()->success()->title('Header Berhasil Disimpan!')->send();
    }
}