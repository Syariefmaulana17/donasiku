<?php

namespace App\Filament\Campaigner\Resources\CampaignResource\Pages;

use App\Filament\Campaigner\Resources\CampaignResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Radio;

class ManageCampaignSettings extends EditRecord
{
    protected static string $resource = CampaignResource::class;
    
    // Ikon gerigi untuk menu pengaturan
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Pengaturan';

    public function getTitle(): string
    {
        return 'Pengaturan Campaign';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('NOMINAL DONASI')
                    ->schema([
                        Toggle::make('allow_manual_nominal')
                            ->label('Nominal Manual')
                            ->helperText('Izinkan donatur mengisi nominal sendiri')
                            ->default(true)
                            ->live(),

                        TextInput::make('min_manual_nominal')
                            ->label('Minimal Nominal Manual')
                            ->placeholder('Kosongkan jika tidak ada batas minimal')
                            ->numeric()
                            ->prefix('Rp')
                            ->helperText('Nominal minimal yang harus diisi donatur ketika mengisi nominal manual. Kosongkan jika tidak ingin membatasi.')
                            ->hidden(fn (Forms\Get $get) => ! $get('allow_manual_nominal')),

                        Repeater::make('nominal_choices')
                            ->label('Pilihan Nominal')
                            ->helperText('Anda bisa menentukan berapa nominal yang disarankan pada halaman form donasi, maksimal 6 opsi.')
                            ->schema([
                                TextInput::make('nominal')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->hiddenLabel(),
                            ])
                            ->addActionLabel('+ Tambah pilihan nominal')
                            ->maxItems(6)
                            ->columns(1),
                    ]),

                Section::make('TAMPILAN PROGRESS DANA')
                    ->schema([
                        Radio::make('progress_display_type')
                            ->label('Dasar tampilan nominal di halaman detail program')
                            ->helperText('Pilih nominal yang ingin ditonjolkan pada progress donasi di halaman detail program.')
                            ->options([
                                'terkumpul' => 'Dana Terkumpul (Menampilkan total dana yang sudah terkumpul dari donatur)',
                                'tersisa' => 'Dana Tersisa (Menampilkan sisa dana yang dibutuhkan hingga target tercapai)',
                            ])
                            ->default('terkumpul'),
                    ]),

                Section::make('CUSTOM META PIXEL')
                    ->schema([
                        TextInput::make('fb_pixel_id')
                            ->label('Facebook Pixel')
                            ->helperText('Jika Anda ingin mendapatkan pixel pengunjung program ini. Silahkan masukkan ID Pixel Facebook Anda di sini.'),

                        TextInput::make('fb_access_token')
                            ->label('Facebook Access Token')
                            ->helperText('Facebook Access Token digunakan untuk menghubungkan sistem dengan Meta.')
                            ->columnSpanFull(),

                        TextInput::make('fb_test_event_code')
                            ->label('Facebook Test Event Code')
                            ->helperText('Test event code akan selalu berubah, pastikan test event code telah sesuai.'),
                    ])
                    ->columns(1),
            ]);
    }
}