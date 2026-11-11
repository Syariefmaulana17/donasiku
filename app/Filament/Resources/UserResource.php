<?php
namespace App\Filament\Resources;

use App\Models\User;
use App\Filament\Resources\UserResource\Pages;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Pengaturan Sistem';
    protected static ?string $navigationLabel = 'Manajemen Admin';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Data Pengguna')->schema([
                TextInput::make('name')->label('Nama')->required(),
                TextInput::make('email')->label('Email')->email()->required(),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),

                // Ini adalah tombol ajaib (Toggle) untuk menyetujui akun
                Toggle::make('is_approved')
                    ->label('Setujui Akses Dasbor')
                    ->onColor('success')
                    ->offColor('danger')
                    ->default(false),
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
            Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
            Tables\Columns\IconColumn::make('is_approved')
                ->label('Disetujui')
                ->boolean(),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Mendaftar Sejak')
                ->dateTime('d M Y')
                ->sortable(),
        ])->actions([ Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make() ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}