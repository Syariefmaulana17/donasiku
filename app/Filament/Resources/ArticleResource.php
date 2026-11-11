<?php

namespace App\Filament\Resources;

use App\Models\Article;
use App\Filament\Resources\ArticleResource\Pages;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Manajemen Artikel';
    protected static ?string $navigationLabel = 'Daftar Artikel';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(3)->schema([
                
                // KOLOM KIRI (KONTEN ARTIKEL)
                Grid::make(1)->schema([
                    Section::make('Tulis Artikel')->schema([
                        TextInput::make('title')->label('Judul Artikel')->required()->maxLength(255)
                            ->live(debounce: 500) // Live digunakan agar Analisa SEO bisa membaca teks seketika
                            ->afterStateUpdated(fn ($set, $state) => $set('slug', Str::slug($state ?? ''))),
                        
                        TextInput::make('slug')->label('Link (Slug)')->required()->unique(ignoreRecord: true),
                        
                        Select::make('article_category_id')->label('Kategori')
                            ->relationship('category', 'name')->searchable()->required(),
                            
                        FileUpload::make('cover_image')->label('Gambar Cover')->image()->disk('public')->directory('articles')->visibility('public'),
                        
                        RichEditor::make('content')->label('Isi Artikel')->required()->live(debounce: 1000),
                    ]),
                ])->columnSpan(2),

                // KOLOM KANAN (SEO & STATUS)
                Grid::make(1)->schema([
                    Section::make('Terbitkan')->schema([
                        Toggle::make('is_published')->label('Tayangkan Artikel')->default(true),
                    ]),

                    Section::make('Yoast SEO Analysis')->schema([
                        TextInput::make('focus_keyword')->label('Focus Keyphrase')->live(debounce: 500)
                            ->placeholder('Contoh: donasi palestina'),
                            
                        TextInput::make('meta_title')->label('Meta Title (Opsional)')->live(debounce: 500),
                        
                        TextInput::make('meta_description')->label('Meta Description')->live(debounce: 500)
                            ->maxLength(160),

                        // ROBOT ANALISA SEO
                        Placeholder::make('seo_score')
                            ->label('Hasil Analisa SEO')
                            ->content(function (Get $get) {
                                $keyword = strtolower($get('focus_keyword') ?? '');
                                $title = strtolower($get('title') ?? '');
                                $content = strtolower(strip_tags($get('content') ?? ''));
                                $metaDesc = $get('meta_description') ?? '';

                                if (!$keyword) {
                                    return new HtmlString('<div class="text-orange-500 text-sm font-bold">⚠️ Masukkan Focus Keyphrase untuk melihat skor.</div>');
                                }

                                $score = 0;
                                $checks = [];

                                // 1. Cek Judul
                                if (str_contains($title, $keyword)) {
                                    $score++;
                                    $checks[] = '✅ Keyphrase ada di Judul';
                                } else {
                                    $checks[] = '❌ Keyphrase tidak ditemukan di Judul';
                                }

                                // 2. Cek Panjang Teks (Minimal 300 kata)
                                $wordCount = str_word_count($content);
                                if ($wordCount >= 300) {
                                    $score++;
                                    $checks[] = '✅ Teks cukup panjang (' . $wordCount . ' kata)';
                                } else {
                                    $checks[] = '❌ Teks terlalu pendek (' . $wordCount . '/300 kata)';
                                }

                                // 3. Cek Keyword di dalam Teks
                                if (str_contains($content, $keyword)) {
                                    $score++;
                                    $checks[] = '✅ Keyphrase ditemukan di dalam teks';
                                } else {
                                    $checks[] = '❌ Keyphrase belum ada di dalam teks artikel';
                                }

                                // 4. Cek Meta Description
                                $descLen = strlen($metaDesc);
                                if ($descLen >= 120 && $descLen <= 160) {
                                    $score++;
                                    $checks[] = '✅ Panjang Meta Description ideal';
                                } else {
                                    $checks[] = '❌ Meta Description harus 120 - 160 karakter';
                                }

                                // Tentukan Warna Skor
                                $statusHtml = $score >= 3 
                                    ? '<span class="text-green-600 font-bold bg-green-50 px-2 py-1 rounded">😊 Bagus (Good)</span>' 
                                    : '<span class="text-red-600 font-bold bg-red-50 px-2 py-1 rounded">☹️ Perlu Perbaikan (Needs Work)</span>';

                                $listHtml = '<ul class="mt-3 space-y-1 text-xs text-gray-600">';
                                foreach ($checks as $c) { $listHtml .= "<li>$c</li>"; }
                                $listHtml .= '</ul>';

                                return new HtmlString($statusHtml . $listHtml);
                            }),
                    ]),
                ])->columnSpan(1),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('cover_image')->label('Cover'),
            Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->limit(30),
            Tables\Columns\TextColumn::make('category.name')->label('Kategori'),
            Tables\Columns\IconColumn::make('is_published')->label('Tayang')->boolean(),
        ])->actions([ Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make() ]);
    }

    // MEMBUAT HALAMAN BARU UNTUK CREATE & EDIT (Tidak menggunakan modal)
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}