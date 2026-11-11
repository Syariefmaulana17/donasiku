<?php

namespace App\Filament\Campaigner\Resources\CampaignResource\Pages;

use App\Filament\Campaigner\Resources\CampaignResource;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ManageCampaignDonations extends ManageRelatedRecords
{
    protected static string $resource = CampaignResource::class;

    protected static string $relationship = 'donations';

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    public function getTitle(): string
    {
        return 'Data Donatur';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Campaigner\Resources\CampaignResource\Widgets\CampaignDateFilterWidget::class,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->modifyQueryUsing(function (Builder $query) {
                $startDate = request('startDate')
                    ? Carbon::parse(request('startDate'))->startOfDay()
                    : now()->subDays(30)->startOfDay();

                $endDate = request('endDate')
                    ? Carbon::parse(request('endDate'))->endOfDay()
                    : now()->endOfDay();

                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('donor_name') 
                    ->label('Donatur')
                    ->searchable()
                    ->weight('bold')
                    ->formatStateUsing(fn ($state, $record) => $record->is_anonymous ? 'Hamba Allah' : $state)
                    ->description(fn ($record) => $record->is_anonymous ? 'NN' : ''),

                Tables\Columns\TextColumn::make('donor_email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('donor_phone')
                    ->label('No. Telp')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float)$state, 0, ',', '.'))
                    ->sortable(),

                // MENGHITUNG BIAYA PLATFORM 10% SECARA OTOMATIS
                Tables\Columns\TextColumn::make('platform_fee')
                    ->label('Biaya Platform')
                    ->getStateUsing(fn ($record) => $record->amount * 0.10)
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float)$state, 0, ',', '.'))
                    ->toggleable(isToggledHiddenByDefault: true), // Disembunyikan agar lega, bisa di-ceklis oleh user

                // MENGHITUNG DONASI BERSIH (NOMINAL - 10%)
                Tables\Columns\TextColumn::make('net_amount')
                    ->label('Donasi Bersih')
                    ->getStateUsing(fn ($record) => $record->amount - ($record->amount * 0.10))
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float)$state, 0, ',', '.'))
                    ->color('success')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc') 
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Donasi Offline')
                    ->icon('heroicon-o-plus'),
                
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function ($livewire) {
                        $query = $livewire->getFilteredTableQuery(); 
                        
                        return response()->streamDownload(function () use ($query) {
                            $file = fopen('php://output', 'w');
                            // Header Excel diubah menjadi Biaya Platform
                            fputcsv($file, ['Tanggal', 'Donatur', 'Email', 'No. Telp', 'Nominal', 'Biaya Platform', 'Donasi Bersih', 'Status']);

                            $query->chunk(500, function ($donations) use ($file) {
                                foreach ($donations as $donation) {
                                    $namaDonatur = $donation->is_anonymous ? 'Hamba Allah' : $donation->donor_name;
                                    
                                    // Kalkulasi otomatis 10% untuk Excel
                                    $biayaPlatform = $donation->amount * 0.10;
                                    $donasiBersih = $donation->amount - $biayaPlatform;
                                    
                                    fputcsv($file, [
                                        $donation->created_at?->format('Y-m-d H:i:s'),
                                        $namaDonatur,
                                        $donation->donor_email,
                                        $donation->donor_phone,
                                        $donation->amount,
                                        $biayaPlatform,
                                        $donasiBersih,
                                        $donation->status,
                                    ]);
                                }
                            });
                            fclose($file);
                        }, 'Data-Donatur-'.now()->format('Ymd-His').'.csv');
                    }),
            ])
            ->bulkActions([])
            ->actions([]); 
    }
}