<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WebSettingResource\Pages;
use App\Models\WebSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WebSettingResource extends Resource
{
    protected static ?string $model = WebSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar'; 
    protected static ?string $navigationLabel = 'Tracking & Analytics';
    protected static ?string $navigationGroup = 'Pengaturan Sistem';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Pengaturan Pelacakan')
                    ->tabs([
                        // --- KAMAR 1: KHUSUS GOOGLE ---
                        Forms\Components\Tabs\Tab::make('Google Tag Manager')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\TextInput::make('gtm_id')
                                    ->label('GTM Container ID')
                                    ->placeholder('Contoh: GTM-XXXXXXX')
                                    ->helperText('Masukkan ID Google Tag Manager untuk melacak interaksi dasar web.')
                                    ->maxLength(255),
                            ]),

                        // --- KAMAR 2: KHUSUS META (FACEBOOK) ---
                        Forms\Components\Tabs\Tab::make('Meta Pixel & CAPI')
                            ->icon('heroicon-o-camera')
                            ->schema([
                                Forms\Components\TextInput::make('pixel_id')
                                    ->label('Meta Pixel ID (Dataset ID)')
                                    ->placeholder('Contoh: 123456789012345')
                                    ->maxLength(255),
                                    
                                Forms\Components\TextInput::make('test_event_code')
                                    ->label('Test Event Code (Opsional)')
                                    ->placeholder('Contoh: TEST12345')
                                    ->helperText('Gunakan ini hanya saat menguji event di Events Manager Meta.')
                                    ->maxLength(255),
                                    
                                Forms\Components\Textarea::make('capi_token')
                                    ->label('Conversions API (CAPI) Access Token')
                                    ->placeholder('Paste token yang sangat panjang dari Meta di sini...')
                                    ->helperText('Token rahasia ini membuat server bisa langsung melapor ke markas Facebook.')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])
                    ->columnSpanFull(), // Agar tab melebar penuh di layar
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gtm_id')
                    ->label('GTM ID')
                    ->searchable()
                    ->badge()
                    ->color('warning'),
                    
                Tables\Columns\TextColumn::make('pixel_id')
                    ->label('Pixel ID')
                    ->searchable()
                    ->badge()
                    ->color('info'),
                    
                // Ceklis otomatis jika CAPI Token sudah diisi
                Tables\Columns\IconColumn::make('capi_token')
                    ->label('Status CAPI')
                    ->icon(fn ($state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->colors([
                        'success' => fn ($state): bool => (bool) $state,
                        'danger' => fn ($state): bool => ! (bool) $state,
                    ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // KOSONG (Anti error intl)
            ])
            ->paginated(false);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebSettings::route('/'),
            'create' => Pages\CreateWebSetting::route('/create'),
            'edit' => Pages\EditWebSetting::route('/{record}/edit'),
        ];
    }
}