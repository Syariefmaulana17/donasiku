<?php

namespace App\Filament\Campaigner\Widgets;

use App\Models\Campaign;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class TopProgramWidget extends BaseWidget
{
    protected static ?int $sort = 3; // Tampil di bawah grafik
    protected int | string | array $columnSpan = 1; // Memakan setengah layar agar bisa berdampingan
    protected static ?string $heading = 'Top Program';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Campaign::query()
                    ->where('user_id', Auth::id())
                    ->withSum(['donations' => fn($q) => $q->where('status', 'success')], 'amount')
                    ->withCount(['donations' => fn($q) => $q->where('status', 'success')])
                    ->orderByDesc('donations_sum_amount')
                    ->limit(5) // Hanya ambil 5 teratas
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Nama Program')
                    // Menambahkan teks jumlah transaksi kecil di bawah judul program
                    ->description(fn (Campaign $record): string => ($record->donations_count ?? 0) . ' transaksi')
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('donations_sum_amount')
                    ->label('Total Raihan')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float)($state ?? 0), 0, ',', '.'))
                    ->alignEnd() // Rata kanan untuk angka uang
                    ->color('primary'),
            ])
            ->paginated(false); // Hilangkan tombol halaman (Next/Prev) agar rapi
    }
}