<?php

namespace App\Filament\Resources;

use Illuminate\Support\Str;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Set;
use App\Filament\Resources\CampaignResource\Pages;
use App\Models\Campaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// --- Peralatan Baru untuk Form & Table ---
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Resources\Pages\Page;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Daftar Program';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Utama')->schema([
                    TextInput::make('title')
                        ->label('Judul Donasi / Campaign')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                    
                    TextInput::make('slug')
                        ->label('Link URL (Slug)')
                        ->required()
                        ->unique(Campaign::class, 'slug', ignoreRecord: true),

                    Hidden::make('user_id')
                        ->default(fn () => auth()->id()),

                    Select::make('category')
                        ->label('Kategori')
                        ->options([
                            'donasi' => 'Donasi Umum',
                            'zakat' => 'Zakat',
                            'infaq' => 'Infaq',
                            'sedekah' => 'Sedekah',
                            'wakaf' => 'Wakaf',
                        ])
                        ->required(),

                    // --- INI GERBANG KURASI (HANYA BISA DIUBAH ADMIN) ---
                    Select::make('status')
                        ->label('Status Persetujuan')
                        ->options([
                            'pending' => 'Menunggu Tinjauan',
                            'active' => 'Tayang (Aktif)',
                            'rejected' => 'Ditolak',
                        ])
                        ->default('pending')
                        ->disabled(fn () => auth()->id() !== 1) // Mengunci kolom ini jika yang login bukan Super Admin (ID 1)
                        ->dehydrated() // Memastikan data tetap tersimpan meski sedang di-disable
                        ->required(),
                    // ----------------------------------------------------

                    FileUpload::make('cover_image')
                        ->label('Gambar Cover')
                        ->image()
                        ->disk('public')
                        ->directory('campaign-covers')
                        ->visibility('public')
                        ->required()
                        ->columnSpanFull(),

                    RichEditor::make('description')
                        ->label('Deskripsi Lengkap')
                        ->required()
                        ->toolbarButtons([
                            'attachFiles', 'blockquote', 'bold', 'bulletList', 
                            'h2', 'h3', 'italic', 'link', 'orderedList', 'redo', 'strike', 'undo'
                        ])
                        ->columnSpanFull(),
                ])->columns(2),

                Section::make('Target & Lokasi')->schema([
                    TextInput::make('target_amount')
                        ->label('Target Donasi (Rp)')
                        ->numeric()
                        ->required()
                        ->prefix('Rp'),

                    DatePicker::make('end_date')
                        ->label('Tenggat Waktu Berakhir'),

                    TextInput::make('location')
                        ->label('Lokasi (Contoh: Bandung, Jawa Barat)'),

                    TextInput::make('gmaps_url')
                        ->label('Link Google Maps')
                        ->url(),
                ])->columns(2),

                Section::make('Pengaturan Lanjutan (Paket & Minimal Donasi)')->schema([
                    Toggle::make('is_package_enabled')
                        ->label('Aktifkan Donasi Per Paket (Varian)')
                        ->live(),

                    Repeater::make('packages')
                        ->label('Daftar Paket')
                        ->schema([
                            TextInput::make('nama_paket')->required(),
                            TextInput::make('nominal')->numeric()->required()->prefix('Rp'),
                            Textarea::make('deskripsi')->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->hidden(fn (Get $get) => ! $get('is_package_enabled'))
                        ->columnSpanFull(),

                    Toggle::make('is_min_donation_enabled')
                        ->label('Set Up Minimal Donasi')
                        ->live(),

                    TextInput::make('min_donation_amount')
                        ->label('Nominal Minimal')
                        ->numeric()
                        ->prefix('Rp')
                        ->hidden(fn (Get $get) => ! $get('is_min_donation_enabled')),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\Campaign::query()
                    // 1. Menghitung Total Donasi Sukses
                    ->withSum(['donations' => fn($query) => $query->where('status', 'success')], 'amount')
                    // 2. PERBAIKAN: Menghitung Total Pencairan dengan status 'completed' (Sesuai gambar Anda)
                    ->withSum(['withdrawals' => fn($query) => $query->where('status', 'completed')], 'amount')
                    // 3. Menghitung Jumlah Donatur
                    ->withCount(['donations' => fn($query) => $query->where('status', 'success')])
            )
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Cover')
                    ->disk('public')
                    ->square()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Donasi')
                    ->searchable()
                    ->sortable() 
                    ->toggleable(),

                Tables\Columns\ImageColumn::make('fundraiser_logo')
                    ->label('Logo')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('fundraiser_name')
                    ->label('Penggalang Dana')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('target_amount')
                    ->label('Target (Rp)')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float)$state, 0, ',', '.'))
                    ->sortable()
                    ->toggleable(),

                // BAGIAN KEUANGAN
                // A. TOTAL DANA KOTOR (Terkumpul)
                Tables\Columns\TextColumn::make('donations_sum_amount')
                    ->label('Terkumpul Kotor (Rp)')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float)($state ?? 0), 0, ',', '.'))
                    ->sortable()
                    ->toggleable(),

                // B. BIAYA ADMIN & PENGEMBANGAN (10%)
                Tables\Columns\TextColumn::make('biaya_admin')
                    ->label('Biaya Admin 10%')
                    ->getStateUsing(function ($record) {
                        $terkumpul = $record->donations_sum_amount ?? 0;
                        return $terkumpul * 0.10; // Rumus mencari 10%
                    })
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float)$state, 0, ',', '.'))
                    ->color('warning') // Warna kuning peringatan potongan
                    ->toggleable(),

                // C. TOTAL PENCAIRAN YANG SUDAH 'COMPLETED'
                Tables\Columns\TextColumn::make('withdrawals_sum_amount')
                    ->label('Pencairan (Rp)')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float)($state ?? 0), 0, ',', '.'))
                    ->sortable()
                    ->color('danger') // Warna merah uang keluar
                    ->toggleable(),

                // D. SALDO BERSIH (Terkumpul - Biaya Admin - Pencairan)
                Tables\Columns\TextColumn::make('saldo')
                    ->label('Saldo Bersih (Rp)')
                    ->getStateUsing(function ($record) {
                        $terkumpul = $record->donations_sum_amount ?? 0;
                        
                        // Potong 10% dulu
                        $potonganAdmin = $terkumpul * 0.10;
                        $terkumpulBersih = $terkumpul - $potonganAdmin;
                        
                        // Kurangi dengan yang sudah dicairkan
                        $dicairkan = $record->withdrawals_sum_amount ?? 0;
                        
                        // Hasil Akhir
                        return $terkumpulBersih - $dicairkan;
                    })
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float)$state, 0, ',', '.'))
                    ->weight('bold')
                    ->color('success') // Warna hijau saldo aman
                    ->toggleable(),

                Tables\Columns\TextColumn::make('donations_count')
                    ->label('Donatur')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter Status Program')
                    ->options([
                        'active' => 'Aktif',
                        'pending' => 'Menunggu',
                        'rejected' => 'Ditolak',
                    ])
                    ->multiple(),
            ], layout: \Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // =========================================================================
    // INI ADALAH JANTUNG ISOLASI DATA (Membatasi hak lihat Campaigner)
    // =========================================================================
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        // Jika yang login BUKAN Super Admin (Asumsi Super Admin adalah akun pertama dengan ID 1)
        if (auth()->id() !== 1) { 
            // Kunci query agar hanya menampilkan data milik user yang sedang login
            $query->where('user_id', auth()->id());
        }
        
        return $query;
    }
    // =========================================================================

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }
    
}