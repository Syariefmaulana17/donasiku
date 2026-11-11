<?php

namespace App\Filament\Widgets;

use App\Models\Donation;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestDonations extends BaseWidget
{
    // Urutan ke-4 (Paling bawah)
    protected static ?int $sort = 4;
    
    // Membuat tabel mengambil lebar layar secara penuh (full span)
    protected int | string | array $columnSpan = 'full';
    
    // Judul Tabel
    protected static ?string $heading = '5 Transaksi Donasi Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Mengambil data donasi terbaru, dan membatasinya hanya 5 baris
                Donation::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('donor_name')
                    ->label('Nama Donatur')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('campaign.title')
                    ->label('Program')
                    ->limit(30), // Dibatasi 30 huruf agar tidak kepanjangan
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->color('success'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'success' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Transaksi')
                    ->dateTime('d M Y - H:i') // Format tanggal dan jam
                    ->description(fn ($record) => $record->created_at->diffForHumans()), // Menambahkan info "5 menit yang lalu"
            ])
            ->paginated(false); // Mematikan fitur halaman (pagination) karena hanya butuh 5 data
    }
}