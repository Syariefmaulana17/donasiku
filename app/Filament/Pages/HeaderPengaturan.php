<?php

namespace App\Filament\Pages;

use App\Models\HomepageSetting;
use App\Models\WebPage;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

// KITA MENGGUNAKAN NAMA CLASS BARU
class HeaderPengaturan extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationGroup = 'Konfigurasi Tampilan';
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    protected static ?int $navigationSort = 1;
    
    // NAMA MENU & JUDUL YANG PASTI BERSIH
    protected static ?string $navigationLabel = 'Header & Navbar';
    protected static ?string $title = 'Header & Navbar';
    
    // URL BARU AGAR SISTEM MEMBUAT JALUR BARU
    protected static ?string $slug = 'header-pengaturan';

    protected static string $view = 'filament.pages.manage-homepage';

    public ?array $data = [];

    public function mount(): void 
    {
        $setting = HomepageSetting::first() ?? HomepageSetting::create();
        $this->form->fill($setting->toArray());
    }

    public function form(Form $form): Form 
    {
        return $form->schema([
            Section::make('Pengaturan Header (Bagian Atas Web)')->schema([
                FileUpload::make('logo')
                    ->label('Logo Website')
                    ->image()
                    ->disk('public')
                    ->directory('web-assets')
                    ->visibility('public'),
                ColorPicker::make('navbar_color')
                    ->label('Warna Background Navbar')
                    ->default('#ffffff')
                    ->required(),
                Repeater::make('navbar_menu')
                    ->label('Menu Navbar')
                    ->schema([
                        TextInput::make('label')
                            ->label('Nama Menu')
                            ->placeholder('Contoh: Beranda, Donasi Rutin')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                if (blank($state)) {
                                    return;
                                }
                                $currentUrl = $get('url');
                                if (blank($currentUrl)) {
                                    $lower = strtolower(trim($state));
                                    if (in_array($lower, ['beranda', 'home', 'halaman utama'], true)) {
                                        $set('url', '/');
                                    } else {
                                        $set('url', '/' . Str::slug($state));
                                    }
                                }
                            })
                            ->required(),
                        Select::make('quick_target')
                            ->label('Pilih Halaman Cepat (Opsional)')
                            ->placeholder('-- Pilih halaman yang sudah ada --')
                            ->options(function (): array {
                                $options = [
                                    '/' => '🏠 Beranda (Halaman Utama)',
                                    '/#program-kebaikan' => '💝 Daftar Program Donasi',
                                    '/#kabar-kebaikan' => '📰 Artikel & Berita',
                                ];

                                try {
                                    $pages = WebPage::where('is_active', true)->orderBy('title')->get();
                                    foreach ($pages as $page) {
                                        $options['/' . ltrim($page->slug, '/')] = '📄 ' . $page->title . ' (/' . ltrim($page->slug, '/') . ')';
                                    }
                                } catch (\Throwable $e) {
                                    // Abaikan jika tabel belum siap
                                }

                                return $options;
                            })
                            ->dehydrated(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                if (filled($state)) {
                                    $set('url', $state);
                                }
                            }),
                        TextInput::make('url')
                            ->label('Link URL / Jalur Halaman')
                            ->placeholder('Contoh: / atau /donasi-rutin')
                            ->helperText('Ketik "/" untuk Beranda, atau "/slug-halaman" (otomatis diperbaiki jika tanpa garis miring).')
                            ->required(),
                    ])->columns(3),
            ])
        ])->statePath('data');
    }

    public function save(): void 
    {
        $state = $this->form->getState();

        if (! empty($state['navbar_menu']) && is_array($state['navbar_menu'])) {
            foreach ($state['navbar_menu'] as $idx => $item) {
                $state['navbar_menu'][$idx]['url'] = HomepageSetting::normalizeNavUrl(
                    $item['url'] ?? '/',
                    $item['label'] ?? null
                );
            }
        }

        $setting = HomepageSetting::first() ?? HomepageSetting::create();
        $setting->update($state);
        $this->form->fill($setting->fresh()->toArray());
        Notification::make()->success()->title('Header Berhasil Disimpan!')->send();
        $this->js('window.location.reload()');
    }
}