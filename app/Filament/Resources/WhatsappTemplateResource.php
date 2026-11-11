<?php

namespace App\Filament\Resources;

use App\Models\WhatsappTemplate;
use App\Filament\Resources\WhatsappTemplateResource\Pages;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WhatsappTemplateResource extends Resource
{
    protected static ?string $model = WhatsappTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';
    protected static ?string $navigationGroup = 'Pengaturan Sistem';
    protected static ?string $navigationLabel = 'Template WA (Tombol)';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Atur Pesan & Tombol')->schema([
                TextInput::make('label')
                    ->label('Teks Tombol (Contoh: "1" atau "Follow Up")')
                    ->required(),
                Textarea::make('message')
                    ->label('Isi Pesan WhatsApp')
                    ->helperText('Gunakan {nama} untuk memanggil nama donatur, dan {nominal} untuk jumlah donasi.')
                    ->required()
                    ->rows(5),
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('label')->label('Nama Tombol'),
            Tables\Columns\TextColumn::make('message')->label('Isi Pesan')->limit(50),
        ])->actions([ Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make() ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWhatsappTemplates::route('/'),
        ];
    }
}