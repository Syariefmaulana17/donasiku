<?php

namespace App\Filament\Campaigner\Resources;

use App\Filament\Campaigner\Resources\DonationResource\Pages;
use App\Models\Donation;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class DonationResource extends Resource
{
    protected static ?string $model = Donation::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Galang Dana';
    protected static ?string $navigationLabel = 'Donatur';
    protected static ?int $navigationSort = 2;
    
    protected static ?string $pluralModelLabel = 'Donatur Saya';

    public static function table(Table $table): Table
    {
        // KOMBINASI YANG PALING MUNGKIN BENAR BERDASARKAN ERROR SEBELUMNYA
        $kolomNama = 'donor_name'; // Terbukti benar
        $kolomHp = 'phone';        // Jika masih error, ganti dengan 'no_hp'
        $kolomEmail = 'donor_email'; // Jika masih error, ganti dengan 'email'

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->whereHas('campaign', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->where('status', 'success')
                ->select(
                    $kolomNama,
                    $kolomHp,
                    $kolomEmail,
                    DB::raw('MAX(id) as id'),
                    DB::raw('COUNT(id) as total_transactions'),
                    DB::raw('SUM(amount) as total_amount'),
                    DB::raw('MAX(created_at) as last_donation')
                )
                ->groupBy($kolomNama, $kolomHp, $kolomEmail)
            )
            ->columns([
                Tables\Columns\TextColumn::make($kolomNama)
                    ->label('Donatur')
                    ->searchable()
                    ->icon('heroicon-m-user-circle')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make($kolomHp)
                    ->label('Phone')
                    ->searchable(),

                Tables\Columns\TextColumn::make($kolomEmail)
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_transactions')
                    ->label('Jumlah Transaksi')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Donasi')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float)$state, 0, ',', '.'))
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('last_donation')
                    ->label('Terakhir Donasi')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->translatedFormat('d M Y'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('segment')
                    ->label('Segmen')
                    ->getStateUsing(function ($record) {
                        $lastDonation = Carbon::parse($record->last_donation);
                        $days = $lastDonation->diffInDays(now());

                        if ($days > 365) return 'Lost';
                        if ($days > 180) return 'Hibernating';
                        if ($days > 90) return 'At Risk';
                        return 'Active';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Lost' => 'gray',
                        'Hibernating' => 'gray',
                        'At Risk' => 'danger',
                        'Active' => 'success',
                        default => 'gray',
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('info_segmen')
                    ->label('Info Segmen')
                    ->icon('heroicon-o-information-circle')
                    ->color('gray')
                    ->modalHeading('Penjelasan Segmen Donatur')
                    ->modalDescription('• Active: Donasi dalam 3 bulan terakhir.
                    • At Risk: Donasi terakhir 3 - 6 bulan yang lalu.
                    • Hibernating: Donasi terakhir 6 - 12 bulan yang lalu.
                    • Lost: Tidak berdonasi lebih dari 1 tahun.')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),

                Tables\Actions\Action::make('download_donatur')
                    ->label('Download Donatur')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function () {
                        Notification::make()
                            ->title('Fitur Download sedang dikembangkan')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDonations::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}