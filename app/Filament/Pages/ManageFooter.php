<?php
namespace App\Filament\Pages;

use App\Models\HomepageSetting;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Notifications\Notification;

class ManageFooter extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationGroup = 'Konfigurasi Tampilan';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-circle';
    protected static ?string $navigationLabel = 'Footer (Bagian Bawah)';
    protected static ?string $title = 'Pengaturan Footer';
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.pages.manage-homepage';

    public ?array $data = [];

    public function mount(): void {
        $this->form->fill(HomepageSetting::first()->toArray());
    }

    public function form(Form $form): Form {
        return $form->schema([
            Section::make('Identitas Footer')->schema([
                FileUpload::make('footer_logo')->label('Logo Footer')->image()->disk('public')->directory('web-assets')->visibility('public'),
                ColorPicker::make('footer_bg_color')->label('Warna Latar Belakang')->default('#fff7ed'),
                Textarea::make('footer_desc')->label('Teks Deskripsi / Visi Misi')->rows(3)->columnSpanFull(),
            ])->columns(2),

            Section::make('Pengaturan Rekening Donasi')->schema([
                Repeater::make('footer_banks')->label('Daftar Rekening Bank (Otomatis Tampil di Checkout & Footer)')
                    ->schema([
                        FileUpload::make('logo')->label('Logo Bank (Opsional)')->image()->disk('public')->directory('web-assets')->visibility('public'),
                        TextInput::make('bank_name')->label('Nama Bank (Contoh: BSI / BCA / Mandiri)')->required(),
                        TextInput::make('account_number')->label('Nomor Rekening')->required(),
                        TextInput::make('account_name')->label('Atas Nama')->required(),
                    ])->columns(4)
            ]),

            Section::make('Informasi Kontak & Lokasi')->schema([
                TextInput::make('footer_phone')->label('Nomor Telepon / CS'),
                TextInput::make('footer_email')->label('Email Resmi'),
                TextInput::make('footer_schedule')->label('Jam Operasional (Contoh: Senin - Sabtu 08:30 - 16:30)'),
                Textarea::make('footer_address')->label('Alamat Lengkap')->rows(3),
                Textarea::make('footer_gmaps')->label('Link Google Maps (Iframe Embed)')->rows(3),
            ])->columns(2),
        ])->statePath('data');
    }

    public function save(): void {
        HomepageSetting::first()->update($this->form->getState());
        Notification::make()->success()->title('Footer Berhasil Disimpan!')->send();
    }
}