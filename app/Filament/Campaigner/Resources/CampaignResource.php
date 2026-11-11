<?php

namespace App\Filament\Campaigner\Resources;

use Illuminate\Support\Str;
use Filament\Forms\Components\Hidden;
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
use Filament\Forms\Set; 
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Builder;

use App\Filament\Campaigner\Resources\CampaignResource\Pages;
use App\Models\Campaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationGroup = 'Galang Dana';
    protected static ?string $navigationLabel = 'Program';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                // --- KOTAK PERINGATAN (Hanya muncul jika Ditolak) ---
                Section::make('⚠️ Program Dikembalikan / Ditolak')
                    ->description('Mohon perbaiki program Anda berdasarkan catatan dari tim admin di bawah ini, lalu klik Simpan untuk menyimpan perubahan.')
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label('Catatan dari Admin')
                            ->disabled() // Dikunci agar Campaigner tidak bisa mengedit pesannya
                            ->columnSpanFull(),
                    ])
                    // Sihir gaib: Hanya muncul jika sedang Edit data ($record ada) DAN statusnya rejected
                    ->visible(fn (?Campaign $record): bool => $record !== null && $record->status === 'rejected')
                    ->extraAttributes(['class' => 'bg-red-50 border-red-500']),
                // ----------------------------------------------------

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

                    FileUpload::make('cover_image')
                        ->label('Gambar Cover')
                        ->image()
                        ->directory('campaign-covers')
                        ->required()
                        ->columnSpanFull(),

                    RichEditor::make('description')
                        ->label('Deskripsi Lengkap (Mendukung HTML, Link, & Gambar)')
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
            ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query
                // Memastikan Campaigner HANYA melihat program miliknya sendiri
                ->where('user_id', auth()->id())
                ->withSum(['donations' => fn($q) => $q->where('status', 'success')], 'amount')
                ->withSum(['withdrawals' => fn($q) => $q->where('status', 'completed')], 'amount')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('cover')
                    ->label('')
                    ->square()
                    ->size(50),

                Tables\Columns\TextColumn::make('title')
                    ->label('Program')
                    ->searchable() // Mengaktifkan fitur "Cari nama program..."
                    ->weight('bold')
                    ->wrap(),

                // Kolom Slug dengan fitur Copy seperti di referensi
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->copyable()
                    ->copyMessage('Slug berhasil disalin!')
                    ->icon('heroicon-m-document-duplicate')
                    ->color('gray'),

                // Kolom Raihan dengan detail Target
                Tables\Columns\TextColumn::make('donations_sum_amount')
                    ->label('Raihan')
                    ->getStateUsing(fn ($record) => $record->donations_sum_amount ?? 0)
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float)$state, 0, ',', '.'))
                    ->description(function (\App\Models\Campaign $record): string {
                        $target = $record->target ?? 0;
                        $terkumpul = $record->donations_sum_amount ?? 0;
                        // Hitung persentase
                        $persen = $target > 0 ? floor(($terkumpul / $target) * 100) : 0;
                        return $persen . '% dari Rp ' . number_format((float)$target, 0, ',', '.');
                    })
                    ->weight('bold'),

                // Kolom Saldo Bersih
                Tables\Columns\TextColumn::make('saldo')
                    ->label('Saldo')
                    ->getStateUsing(function ($record) {
                        $terkumpul = $record->donations_sum_amount ?? 0;
                        $potonganAdmin = $terkumpul * 0.10;
                        $dicairkan = $record->withdrawals_sum_amount ?? 0;
                        return ($terkumpul - $potonganAdmin) - $dicairkan;
                    })
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float)$state, 0, ',', '.'))
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y'),

                // Kolom Status dengan terjemahan bahasa Indonesia
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        'completed' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Dipublikasi',
                        'pending' => 'Menunggu',
                        'rejected' => 'Ditolak',
                        'completed' => 'Berakhir',
                        default => ucfirst($state),
                    }),
            ])
            ->filters([
                // Filter Dropdown Status di atas tabel
                Tables\Filters\SelectFilter::make('status')
                    ->label('Semua status')
                    ->options([
                        'active' => 'Dipublikasi',
                        'pending' => 'Menunggu',
                        'rejected' => 'Ditolak',
                        'completed' => 'Berakhir',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Ubah'),
            ]);
    }

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
            'view' => Pages\ViewCampaign::route('/{record}'),
            
            // --- RUTE TAB BARU ---
            'donations' => Pages\ManageCampaignDonations::route('/{record}/donations'),
            'updates' => Pages\ManageCampaignUpdates::route('/{record}/updates'),
            'withdrawals' => Pages\ManageCampaignWithdrawals::route('/{record}/withdrawals'), // <-- Tambahkan ini
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
            'settings' => Pages\ManageCampaignSettings::route('/{record}/settings'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewCampaign::class,
            Pages\ManageCampaignUpdates::class,
            Pages\ManageCampaignDonations::class,
            Pages\ManageCampaignWithdrawals::class,
            Pages\EditCampaign::class,
            Pages\ManageCampaignSettings::class,
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }
}