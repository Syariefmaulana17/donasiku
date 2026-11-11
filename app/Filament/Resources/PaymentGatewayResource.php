<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentGatewayResource\Pages;
use App\Models\PaymentGateway;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentGatewayResource extends Resource
{
    protected static ?string $model = PaymentGateway::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationLabel = 'Kunci API Pembayaran';
    protected static ?string $navigationGroup = 'Pengaturan Sistem';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Payment Gateway')
                    ->description('Masukkan kunci API (API Keys) dari penyedia layanan pembayaran Anda.')
                    ->schema([
                        Forms\Components\Select::make('name')
                            ->label('Penyedia Layanan (Gateway)')
                            ->options([
                                'midtrans' => 'Midtrans',
                                'xendit' => 'Xendit',
                                'doku' => 'Doku',
                                'flip' => 'Flip',
                                'duitku' => 'Duitku',
                                'tripay' => 'Tripay',
                            ])
                            ->required()
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktifkan Gateway Ini?')
                            ->default(false)
                            ->helperText('Hanya gateway yang aktif yang akan digunakan di halaman donasi.'),
                        
                        Forms\Components\TextInput::make('public_key')
                            ->label('Public Key / Client Key / API Key')
                            ->helperText('Paste Public Key / Client Key dari dashboard gateway Anda di sini.')
                            ->maxLength(255)
                            ->columnSpanFull(),
                            
                        Forms\Components\TextInput::make('secret_key')
                            ->label('Secret Key / Server Key')
                            ->helperText('Paste Secret Key / Server Key di sini. Disembunyikan demi keamanan.')
                            ->password() // Mengubahnya jadi titik-titik (rahasia)
                            ->revealable() // Tombol mata untuk melihat teks
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Gateway')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->searchable()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Status Aktif'),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // KITA KOSONGKAN BAGIAN INI UNTUK MENGHINDARI ERROR INTL
            ])
            ->paginated(false); // KITA MATIKAN PAGINATION JUGA
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentGateways::route('/'),
            'create' => Pages\CreatePaymentGateway::route('/create'),
            'edit' => Pages\EditPaymentGateway::route('/{record}/edit'),
        ];
    }
}