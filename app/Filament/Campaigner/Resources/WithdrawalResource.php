<?php

namespace App\Filament\Campaigner\Resources;

use App\Filament\Campaigner\Resources\WithdrawalResource\Pages;
use App\Models\Withdrawal;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class WithdrawalResource extends Resource
{
    protected static ?string $model = Withdrawal::class;

    // 1. Masuk ke dropdown Galang Dana
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Galang Dana';
    protected static ?string $navigationLabel = 'Pencairan';
    protected static ?string $pluralModelLabel = 'Riwayat Pencairan';
    protected static ?int $navigationSort = 3; 
    
    public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                // 1. KOTAK PEMILIHAN PROGRAM
                \Filament\Forms\Components\Section::make('Program')
                    ->description('Pilih program yang dananya akan dicairkan. Saldo dan ketentuan program akan dimuat otomatis.')
                    ->schema([
                        \Filament\Forms\Components\Select::make('campaign_id')
                            ->label('Pilih program')
                            ->options(function () {
                                // Mengambil daftar program milik campaigner yang sedang login
                                return \App\Models\Campaign::where('user_id', \Illuminate\Support\Facades\Auth::id())
                                    ->pluck('title', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->live() // KUNCI UTAMA: Membuat form reaktif saat program dipilih
                            ->afterStateUpdated(fn (callable $set) => $set('amount', null)), // Reset nominal jika ganti program
                    ]),

                // 2. KOTAK RINCIAN & FORM INPUT (Sembunyi jika program belum dipilih)
                \Filament\Forms\Components\Grid::make(3)
                    ->hidden(fn (\Filament\Forms\Get $get) => ! $get('campaign_id'))
                    ->schema([
                        // Kiri: Rincian Saldo (1 Kolom)
                        \Filament\Forms\Components\Section::make('Dana yang dapat dicairkan')
                            ->columnSpan(1)
                            ->schema([
                                \Filament\Forms\Components\Placeholder::make('rincian_saldo')
                                    ->label('')
                                    ->content(function (\Filament\Forms\Get $get) {
                                        $campaign = \App\Models\Campaign::find($get('campaign_id'));
                                        if (!$campaign) return '';

                                        // Kalkulasi keuangan khusus program yang dipilih
                                        $totalRaihan = \App\Models\Donation::where('campaign_id', $campaign->id)
                                            ->where('status', 'success')
                                            ->sum('amount');
                                            
                                        $adminFee = $totalRaihan * 0.10; // Asumsi admin 10%
                                        
                                        $sudahDicairkan = \App\Models\Withdrawal::where('campaign_id', $campaign->id)
                                            ->whereIn('status', ['completed', 'pending'])
                                            ->sum('amount');
                                            
                                        $saldo = $totalRaihan - $adminFee - $sudahDicairkan;
                                        $saldo = $saldo > 0 ? $saldo : 0;

                                        // Menampilkan HTML untuk rincian
                                        return new \Illuminate\Support\HtmlString('
                                            <div class="space-y-3 text-sm">
                                                <div class="flex justify-between font-bold text-2xl text-primary-600 mb-4">
                                                    <span>Rp ' . number_format($saldo, 0, ',', '.') . '</span>
                                                </div>
                                                <div class="flex justify-between border-b pb-2">
                                                    <span class="text-gray-500">Total raihan</span>
                                                    <span>Rp ' . number_format($totalRaihan, 0, ',', '.') . '</span>
                                                </div>
                                                <div class="flex justify-between border-b pb-2">
                                                    <span class="text-gray-500">Biaya Admin (10%)</span>
                                                    <span class="text-red-500">- Rp ' . number_format($adminFee, 0, ',', '.') . '</span>
                                                </div>
                                                <div class="flex justify-between border-b pb-2">
                                                    <span class="text-gray-500">Sudah dicairkan</span>
                                                    <span class="text-red-500">- Rp ' . number_format($sudahDicairkan, 0, ',', '.') . '</span>
                                                </div>
                                            </div>
                                        ');
                                    }),
                            ]),

                        // Kanan: Form Input Pencairan (2 Kolom)
                        \Filament\Forms\Components\Section::make('Form Pencairan Donasi')
                            ->columnSpan(2)
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('amount')
                                    ->label('Jumlah pencairan')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    // Validasi mutlak agar tidak bisa menarik lebih dari saldo
                                    ->rules([
                                        fn (\Filament\Forms\Get $get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                                            $campaign = \App\Models\Campaign::find($get('campaign_id'));
                                            if (!$campaign) return;

                                            $totalRaihan = \App\Models\Donation::where('campaign_id', $campaign->id)->where('status', 'success')->sum('amount');
                                            $adminFee = $totalRaihan * 0.10;
                                            $sudahDicairkan = \App\Models\Withdrawal::where('campaign_id', $campaign->id)->whereIn('status', ['completed', 'pending'])->sum('amount');
                                            $saldo = $totalRaihan - $adminFee - $sudahDicairkan;

                                            if ($value > $saldo) {
                                                $fail("Jumlah pencairan tidak boleh melebihi saldo maksimal (Rp " . number_format($saldo, 0, ',', '.') . ").");
                                            }
                                        },
                                    ]),

                                // Jika Anda belum punya tabel khusus untuk menyimpan Rekening Bank, 
                                // kita gunakan input text biasa yang dikelompokkan agar rapi.
                                \Filament\Forms\Components\Grid::make(2)
                                    ->schema([
                                        \Filament\Forms\Components\TextInput::make('bank_name')
                                            ->label('Nama Bank')
                                            ->placeholder('Contoh: BSI, BCA, Mandiri')
                                            ->required(),
                                        \Filament\Forms\Components\TextInput::make('account_number')
                                            ->label('Nomor Rekening')
                                            ->required()
                                            ->numeric(),
                                        \Filament\Forms\Components\TextInput::make('account_name')
                                            ->label('Nama Pemilik Rekening')
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),

                                // Ganti 'description' dengan nama kolom database Anda untuk rencana penggunaan
                                \Filament\Forms\Components\RichEditor::make('description') 
                                    ->label('Rencana penggunaan dana')
                                    ->placeholder('Penjelasan rencana penggunaan dana...')
                                    ->required()
                                    ->toolbarButtons([
                                        'bold', 'italic', 'underline', 'bulletList', 'orderedList',
                                    ]),
                                    
                                // Menyimpan status default sebagai 'pending' secara otomatis
                                \Filament\Forms\Components\Hidden::make('status')
                                    ->default('pending'),
                            ]),
                    ]),
            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            // 2. Isolasi data: Campaigner HANYA melihat pencairannya sendiri
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->whereHas('campaign', function ($q) {
                    $q->where('user_id', Auth::id());
                })
                ->latest()
            )
            ->columns([
                Tables\Columns\ImageColumn::make('campaign.cover')
                    ->label('Program')
                    ->square()
                    ->size(50),
                    
                Tables\Columns\TextColumn::make('campaign.title')
                    ->label('')
                    ->description(fn (Withdrawal $record) => 'INV-WD-' . $record->id)
                    ->searchable()
                    ->weight('bold')
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float)$state, 0, ',', '.'))
                    ->color('warning') // Warna oranye seperti referensi
                    ->weight('bold')
                    ->sortable(),

                // Sesuaikan 'bank_name', 'account_number', 'account_name' dengan nama kolom database Anda
                Tables\Columns\TextColumn::make('bank_name')
                    ->label('Tujuan')
                    ->description(fn (Withdrawal $record) => $record->account_number . ' a.n ' . $record->account_name)
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'completed' => 'Selesai',
                        'pending' => 'Diproses',
                        'rejected' => 'Ditolak',
                        default => ucfirst($state),
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Semua status')
                    ->options([
                        'completed' => 'Selesai',
                        'pending' => 'Diproses',
                        'rejected' => 'Ditolak',
                    ]),
                    
                // TAMBAHAN BARU: Filter Rentang Tanggal
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('Dari Tanggal')
                            ->maxDate(now()),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('Sampai Tanggal')
                            ->maxDate(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    // Membuat indikator teks yang rapi saat filter aktif
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['created_from'] && ! $data['created_until']) {
                            return null;
                        }

                        $from = $data['created_from'] ? \Carbon\Carbon::parse($data['created_from'])->translatedFormat('d M Y') : 'Awal';
                        $until = $data['created_until'] ? \Carbon\Carbon::parse($data['created_until'])->translatedFormat('d M Y') : 'Sekarang';

                        return "{$from} - {$until}";
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWithdrawals::route('/'),
            'create' => Pages\CreateWithdrawal::route('/create'),
        ];
    }
}