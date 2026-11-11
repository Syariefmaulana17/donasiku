<?php

namespace App\Filament\Campaigner\Widgets;

use App\Models\Donation;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TopDonaturWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 1;
    protected static ?string $heading = 'Top Donatur';

    public function table(Table $table): Table
    {
        // Pastikan nama kolom ini sudah sesuai dengan database Anda
        $kolomNama = 'donor_name'; 

        return $table
            ->query(
                Donation::query()
                    ->whereHas('campaign', function ($query) {
                        $query->where('user_id', Auth::id());
                    })
                    ->where('status', 'success')
                    ->select(
                        $kolomNama,
                        // SOLUSI: Menyediakan kolom 'id' buatan agar tabel Filament tidak error
                        DB::raw('MAX(id) as id'), 
                        DB::raw('SUM(amount) as total_amount'), 
                        DB::raw('COUNT(id) as total_transactions')
                    )
                    ->groupBy($kolomNama)
                    ->orderByDesc('total_amount')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make($kolomNama)
                    ->label('Nama Donatur')
                    ->description(fn (Donation $record): string => $record->total_transactions . 'x donasi')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Donasi')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float)$state, 0, ',', '.'))
                    ->alignEnd()
                    ->color('success'),
            ])
            ->paginated(false);
    }
}