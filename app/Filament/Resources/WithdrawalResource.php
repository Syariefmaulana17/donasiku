<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithdrawalResource\Pages;
use App\Models\Withdrawal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;

class WithdrawalResource extends Resource
{
    protected static ?string $model = Withdrawal::class;

    // Ikon dan Label
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Request Pencairan';
    
    // MENGHILANGKAN GROUP AGAR SEJAJAR DENGAN DATA DONASI
    // Menggunakan navigationSort agar posisinya berada tepat di bawah Data Donasi
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Informasi Pencairan')->schema([
                    
                    // 1. Pilihan Program Donasi (Dibuat Live agar bisa memicu perhitungan)
                    \Filament\Forms\Components\Select::make('campaign_id')
                        ->relationship('campaign', 'title')
                        ->label('Program Donasi')
                        ->required()
                        ->live(), // Penting: Membuat form reaktif saat program dipilih

                    // 2. Kolom Pengisian Nominal Pencairan
                    \Filament\Forms\Components\TextInput::make('amount') // Sesuaikan jika nama kolom Anda berbeda
                        ->label('Nominal Request Pencairan (Rp)')
                        ->required()
                        ->numeric()
                        ->prefix('Rp')
                        // Mencegah input nominal di bawah Rp 10.000 (opsional, bisa Anda sesuaikan)
                        ->minValue(10000) 
                        
                        // LOGIKA BATAS MAKSIMAL & TEKS BANTUAN
                        ->maxValue(function (\Filament\Forms\Get $get) {
                            $campaignId = $get('campaign_id');
                            if (!$campaignId) return 0;

                            // Kita panggil ulang rumus dari Langkah 1
                            $campaign = \App\Models\Campaign::withSum(['donations' => fn($q) => $q->where('status', 'success')], 'amount')
                                ->withSum(['withdrawals' => fn($q) => $q->where('status', 'completed')], 'amount')
                                ->find($campaignId);

                            if (!$campaign) return 0;

                            $terkumpul = $campaign->donations_sum_amount ?? 0;
                            $potonganAdmin = $terkumpul * 0.10;
                            $dicairkan = $campaign->withdrawals_sum_amount ?? 0;
                            
                            $saldoBersih = $terkumpul - $potonganAdmin - $dicairkan;
                            
                            return $saldoBersih > 0 ? $saldoBersih : 0;
                        })
                        ->helperText(function (\Filament\Forms\Get $get) {
                            $campaignId = $get('campaign_id');
                            if (!$campaignId) return 'Pilih program donasi di atas terlebih dahulu untuk melihat batas saldo.';

                            // Hitung ulang untuk ditampilkan sebagai teks
                            $campaign = \App\Models\Campaign::withSum(['donations' => fn($q) => $q->where('status', 'success')], 'amount')
                                ->withSum(['withdrawals' => fn($q) => $q->where('status', 'completed')], 'amount')
                                ->find($campaignId);

                            $terkumpul = $campaign->donations_sum_amount ?? 0;
                            $potonganAdmin = $terkumpul * 0.10;
                            $dicairkan = $campaign->withdrawals_sum_amount ?? 0;
                            
                            $saldoBersih = $terkumpul - $potonganAdmin - $dicairkan;
                            $saldoBersih = $saldoBersih > 0 ? $saldoBersih : 0;

                            return 'Saldo bersih yang bisa dicairkan: Rp ' . number_format($saldoBersih, 0, ',', '.');
                        }),

                    // Kolom opsional: Keterangan / Tujuan Pencairan
                    \Filament\Forms\Components\Textarea::make('description')
                        ->label('Keterangan / Tujuan Pencairan')
                        ->rows(3)
                        ->nullable(),
                        
                    // Hanya Super Admin yang biasanya bisa mengubah status langsung di form
                    \Filament\Forms\Components\Select::make('status')
                        ->label('Status Pencairan')
                        ->options([
                            'pending' => 'Menunggu Persetujuan',
                            'completed' => 'Berhasil Dicairkan',
                            'rejected' => 'Ditolak',
                        ])
                        ->default('pending')
                        // Jika ingin menyembunyikan status ini dari campaigner, buka komentar di bawah ini:
                        // ->hidden(fn () => auth()->user()->role !== 'super_admin') 
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('campaign.title')
                    ->label('Program Donasi')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('campaign.user.name')
                    ->label('Penggalang Dana')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal Request')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float)$state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'completed' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Request')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // --- TOMBOL PROSES / EDIT AJAIB ---
                Tables\Actions\EditAction::make('proses')
                    ->label(fn ($record) => $record->status === 'pending' ? 'Proses' : 'Edit')
                    ->icon('heroicon-o-check-badge')
                    ->color(fn ($record) => $record->status === 'completed' ? 'gray' : 'success')
                    ->modalHeading('Proses Pencairan Dana')
                    ->modalWidth('lg')
                    ->form([
                        // Info Rekening & Tujuan (Mode Baca)
                        Forms\Components\Section::make('Informasi Permintaan')
                            ->schema([
                                Forms\Components\TextInput::make('amount')
                                    ->label('Nominal Request (Rp)')
                                    ->disabled(),
                                Forms\Components\Textarea::make('purpose')
                                    ->label('Tujuan Pencairan')
                                    ->disabled()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('bank_name')
                                    ->label('Bank Tujuan')
                                    ->disabled(),
                                Forms\Components\TextInput::make('account_number')
                                    ->label('Nomor Rekening')
                                    ->disabled(),
                                Forms\Components\TextInput::make('account_name')
                                    ->label('Nama Pemilik')
                                    ->disabled(),
                            ])->columns(2),

                        // Form Keputusan Eksekusi
                        Forms\Components\Section::make('Eksekusi Pencairan')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Keputusan')
                                    ->options([
                                        'pending' => 'Menunggu Diproses',
                                        'completed' => 'Selesai & Sudah Ditransfer',
                                        'rejected' => 'Tolak',
                                    ])
                                    ->required()
                                    ->live(), // Wajib live agar kolom di bawahnya bereaksi
                                
                                Forms\Components\TextInput::make('platform_fee')
                                    ->label('Potongan Biaya Admin / Gateway (Rp)')
                                    ->numeric()
                                    ->default(0)
                                    ->visible(fn (Get $get) => $get('status') === 'completed'),
                                    
                                Forms\Components\FileUpload::make('transfer_proof')
                                    ->label('Upload Bukti Transfer')
                                    ->image()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('bukti-pencairan')
                                    ->visible(fn (Get $get) => $get('status') === 'completed'),
                                    
                                Forms\Components\Textarea::make('rejection_reason')
                                    ->label('Alasan Penolakan')
                                    ->visible(fn (Get $get) => $get('status') === 'rejected'),
                            ]),
                    ]),
                    
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWithdrawals::route('/'),
        ];
    }
}