<?php
namespace App\Filament\Resources;

use App\Models\WebPage;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Builder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Filament\Pages\ManageHomepage; // Meminjam Builder dari Halaman Utama
use App\Filament\Resources\WebPageResource\Pages;

class WebPageResource extends Resource
{
    protected static ?string $model = WebPage::class;
    
    protected static ?string $navigationGroup = 'Konfigurasi Tampilan';
    protected static ?string $navigationIcon = 'heroicon-o-document-plus';
    protected static ?string $navigationLabel = 'Halaman Tambahan (Pages)';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Halaman')->schema([
                TextInput::make('title')->label('Judul Halaman')->required()
                    ->live(onBlur: true)->afterStateUpdated(fn ($set, $state) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('slug')
                    ->label('Link URL (Slug)')
                    ->prefix(url('/') . '/')
                    ->helperText('Mudah ditebak! Halaman ini bisa diakses langsung melalui ' . url('/nama-slug') . ' maupun ' . url('/halaman/nama-slug'))
                    ->required()
                    ->unique(ignoreRecord: true),
                Toggle::make('is_active')->label('Tayangkan Halaman?')->default(true),
            ])->columns(2),

            Section::make('Susun Konten Halaman')->schema([
                Builder::make('content')
                    ->label('Tambahkan Section')
                    ->blocks(ManageHomepage::getPageBlocks()) // Menggunakan block yang sama dengan Homepage!
                    ->collapsible(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')->label('Judul')->searchable(),
            Tables\Columns\TextColumn::make('slug')->label('Link URL')
                ->formatStateUsing(fn ($state) => url('/' . ltrim((string) $state, '/')))
                ->copyable()->copyMessage('Link disalin!')->icon('heroicon-o-link'),
            // Simulasi analitik (karena belum ada tabel tracking)
            Tables\Columns\TextColumn::make('views')->label('Engagement (Views)')
                ->default('0 Kali Dilihat')->badge()->color('info'),
            Tables\Columns\IconColumn::make('is_active')->label('Status Tayang')->boolean(),
        ])->actions([ Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make() ]);
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListWebPages::route('/'),
            'create' => Pages\CreateWebPage::route('/create'),
            'edit' => Pages\EditWebPage::route('/{record}/edit'),
        ];
    }
}