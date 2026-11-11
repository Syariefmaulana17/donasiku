<?php

namespace App\Filament\Campaigner\Resources\CampaignResource\Pages;

use App\Filament\Campaigner\Resources\CampaignResource;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ManageCampaignUpdates extends ManageRelatedRecords
{
    protected static string $resource = CampaignResource::class;

    protected static string $relationship = 'updates';

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    public function getTitle(): string
    {
        return 'Berita / Kabar Terbaru';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Judul Kabar')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                    
                RichEditor::make('content')
                    ->label('Isi Kabar (Mendukung Teks & Gambar)')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Berita')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diterbitkan Pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->paginated(false)
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Tulis Kabar Baru'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}