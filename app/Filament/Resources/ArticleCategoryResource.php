<?php

namespace App\Filament\Resources;

use App\Models\ArticleCategory;
use App\Filament\Resources\ArticleCategoryResource\Pages;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticleCategoryResource extends Resource
{
    protected static ?string $model = ArticleCategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Manajemen Artikel';
    protected static ?string $navigationLabel = 'Kategori Artikel';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                TextInput::make('name')->label('Nama Kategori')->required()
                    ->live(onBlur: true)->afterStateUpdated(fn ($set, $state) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('slug')->label('Link Kategori (Slug)')->required()->unique(ignoreRecord: true),
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Nama Kategori')->searchable(),
            Tables\Columns\TextColumn::make('articles_count')->counts('articles')->label('Jumlah Artikel'),
        ])->actions([ Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make() ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticleCategories::route('/'),
        ];
    }
}