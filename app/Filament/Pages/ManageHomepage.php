<?php
namespace App\Filament\Pages;

use App\Models\HomepageSetting;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Notifications\Notification;

class ManageHomepage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationGroup = 'Konfigurasi Tampilan';
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Halaman Utama (Beranda)';
    protected static ?string $title = 'Pengaturan Beranda';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.manage-homepage';

    public ?array $data = [];

    public function mount(): void {
        $this->form->fill(HomepageSetting::first()->toArray());
    }

    // --- KOTAK PERKAKAS SECTION BUILDER ---
    public static function getPageBlocks(): array
    {
        return [
            Block::make('banner_slider')->label('Banner Slider (Bisa Geser)')->icon('heroicon-m-photo')->schema([
                Repeater::make('slides')->schema([
                    FileUpload::make('image')->label('Upload Banner')->image()->disk('public')->directory('web-assets')->visibility('public')->required(),
                    TextInput::make('link_url')->label('Link Tujuan (Saat Diklik)')->url(),
                ])->columns(2)
            ]),
            Block::make('banner_statis')->label('Banner Statis (1 Gambar)')->icon('heroicon-m-stop')->schema([
                FileUpload::make('image')->label('Upload Banner')->image()->disk('public')->directory('web-assets')->visibility('public')->required(),
                TextInput::make('link_url')->label('Link Tujuan (Saat Diklik)')->url(),
            ]),
            Block::make('category_buttons')->label('Tombol Kategori')->icon('heroicon-m-squares-2x2')->schema([
                Repeater::make('buttons')->schema([
                    TextInput::make('icon_svg')->label('Kode Icon SVG'),
                    TextInput::make('title')->label('Nama')->required(),
                    TextInput::make('link')->label('Link URL')->required(),
                ])->columns(3)
            ]),
            Block::make('campaign_list')->label('Daftar Program Donasi')->icon('heroicon-m-heart')->schema([
                TextInput::make('title')->label('Judul Section')->required(),
                TextInput::make('subtitle')->label('Sub Judul'),
                Select::make('category_filter')->label('Kategori')
                    ->options(['semua' => 'Semua', 'donasi' => 'Donasi', 'zakat' => 'Zakat', 'infaq' => 'Infaq', 'sedekah' => 'Sedekah', 'wakaf' => 'Wakaf'])->default('semua'),
            ])->columns(3),
            Block::make('article_list')->label('Daftar Artikel / Berita')->icon('heroicon-m-newspaper')->schema([
                TextInput::make('title')->label('Judul Section')->required(),
                TextInput::make('subtitle')->label('Sub Judul'),
            ])->columns(2),
            Block::make('html_content')->label('Teks Bebas / HTML')->icon('heroicon-m-document-text')->schema([
                RichEditor::make('content')->label('Isi Konten')->required(),
            ]),
        ];
    }

    public function form(Form $form): Form {
        return $form->schema([
            Section::make('Susun Halaman Utama Anda')->schema([
                Builder::make('dynamic_sections')
                    ->label('Daftar Section (Seret untuk mengubah urutan)')
                    ->blocks(self::getPageBlocks())
                    ->collapsible(),
            ])
        ])->statePath('data');
    }

    public function save(): void {
        HomepageSetting::first()->update($this->form->getState());
        Notification::make()->success()->title('Beranda Berhasil Disimpan!')->send();
    }
}