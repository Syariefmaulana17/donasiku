<?php

namespace App\Filament\Campaigner\Resources\CampaignResource\Pages;

use App\Filament\Campaigner\Resources\CampaignResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Carbon\Carbon;

class ViewCampaign extends ViewRecord
{
    protected static string $resource = CampaignResource::class;

    // 1. Mengosongkan tombol header bawaan
    protected function getHeaderActions(): array
    {
        return []; 
    }

    // 2. Memanggil Widget Filter Tanggal di bagian paling atas halaman (Hanya ada SATU fungsi ini sekarang)
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Campaigner\Resources\CampaignResource\Widgets\CampaignDateFilterWidget::class,
        ];
    }

    // 3. Menampilkan Banner Informasi Program
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        Split::make([
                            ImageEntry::make('cover_image') 
                                ->hiddenLabel()
                                ->size(220) 
                                ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->title ?? 'Program') . '&background=random&size=220')
                                ->extraImgAttributes(['class' => 'rounded-xl object-cover shadow-md'])
                                ->grow(false),

                            Grid::make(1)
                                ->schema([
                                    TextEntry::make('title')
                                        ->hiddenLabel()
                                        ->weight(FontWeight::Bold)
                                        ->size(TextEntry\TextEntrySize::Large),

                                    Grid::make(4)
                                        ->schema([
                                            TextEntry::make('created_at')
                                                ->label('Dibuat')
                                                ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->diffForHumans() : '-'),

                                            TextEntry::make('target')
                                                ->label('Target')
                                                ->formatStateUsing(fn ($state) => $state ? 'Rp ' . number_format($state, 0, ',', '.') : 'Donasi terus dikumpulkan'),

                                            TextEntry::make('end_date') 
                                                ->label('Sisa Hari')
                                                ->formatStateUsing(function ($state) {
                                                    if (!$state) return '∞';
                                                    $days = Carbon::parse($state)->diffInDays(now());
                                                    return $days > 0 ? $days . ' hari lagi' : 'Berakhir';
                                                }),

                                            TextEntry::make('status')
                                                ->label('Status')
                                                ->badge()
                                                ->color(fn (string $state): string => match ($state) {
                                                    'active' => 'success',
                                                    'pending' => 'warning',
                                                    'rejected' => 'danger',
                                                    'completed' => 'gray',
                                                    default => 'gray',
                                                }),
                                        ]),

                                    Actions::make([
                                        Action::make('salin_link')
                                            ->label('Salin Link')
                                            ->icon('heroicon-o-link')
                                            ->color('gray')
                                            ->extraAttributes(fn ($record) => [
                                                'x-on:click' => "window.navigator.clipboard.writeText('".url('/campaign/' . $record->slug)."'); \$tooltip('Link disalin!');",
                                            ]),
                                            
                                        Action::make('lihat_program')
                                            ->label('Lihat Program')
                                            ->icon('heroicon-o-arrow-top-right-on-square')
                                            ->url(fn ($record) => url('/campaign/' . $record->slug))
                                            ->openUrlInNewTab()
                                            ->color('primary'),
                                    ]),
                                ])
                        ])->from('md') 
                    ])
            ]);
    }

    // 4. Memanggil 3 Widget Analitik di bagian bawah banner
    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Campaigner\Resources\CampaignResource\Widgets\CampaignOverviewStats::class,
            \App\Filament\Campaigner\Resources\CampaignResource\Widgets\CampaignDonationChart::class,
            \App\Filament\Campaigner\Resources\CampaignResource\Widgets\CampaignDetailedStats::class,
        ];
    }
}