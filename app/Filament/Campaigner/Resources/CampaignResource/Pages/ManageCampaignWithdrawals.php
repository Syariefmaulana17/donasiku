<?php

namespace App\Filament\Campaigner\Resources\CampaignResource\Pages;

use App\Filament\Campaigner\Resources\CampaignResource;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;

class ManageCampaignWithdrawals extends ManageRelatedRecords
{
    protected static string $resource = CampaignResource::class;
    
    protected static string $relationship = 'withdrawals';
    
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Pencairan Dana';
    protected ?string $heading = 'Kelola Pencairan Dana';

    // 1. MEMANGGIL WIDGET BUKU TABUNGAN DI ATAS TABEL
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Campaigner\Resources\CampaignResource\Widgets\WithdrawalOverviewWidget::class,
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->label('Nominal Pencairan (Rp)')
                    ->numeric()
                    ->required()
                    ->prefix('Rp'),
                Forms\Components\Textarea::make('purpose')
                    ->label('Tujuan Pencairan (Jelaskan rincian penggunaan dana)')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('bank_name')
                    ->label('Nama Bank (Contoh: BCA, Mandiri, BSI)')
                    ->required(),
                Forms\Components\TextInput::make('account_number')
                    ->label('Nomor Rekening')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('account_name')
                    ->label('Nama Pemilik Rekening')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                // Menggabungkan Tanggal dan ID Invoice
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal & Invoice')
                    ->dateTime('d M Y, H:i')
                    ->description(fn ($record) => 'INV-WD-' . str_pad($record->id, 6, '0', STR_PAD_LEFT))
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->weight('bold')
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float)$state, 0, ',', '.')),
                
                // Menggabungkan Data Bank ke dalam 1 Kolom
                Tables\Columns\TextColumn::make('bank_name')
                    ->label('Tujuan Transfer')
                    ->weight('bold')
                    ->description(fn ($record) => $record->account_number . ' a.n ' . $record->account_name),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'info',
                        'completed' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                
                Tables\Columns\ImageColumn::make('transfer_proof')
                    ->label('Bukti Transfer')
                    ->square(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Request Pencairan Baru')
                    ->icon('heroicon-o-plus-circle'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }
}