<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonationResource\Pages;
use App\Models\Donation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use App\Models\WhatsappTemplate;

class DonationResource extends Resource
{
    protected static ?string $model = Donation::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Data Donasi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('campaign_id')
                    ->relationship('campaign', 'title')
                    ->label('Program Donasi')
                    ->required(),
                Forms\Components\TextInput::make('donor_name')
                    ->label('Nama Donatur')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('donor_email')
                    ->label('Email Donatur (Opsional)')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('No. WhatsApp')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('payment_method')
                    ->label('Metode Pembayaran (Contoh: VA BCA)')
                    ->maxLength(255),
                Forms\Components\TextInput::make('payment_gateway')
                    ->label('Payment Gateway (Contoh: Midtrans)')
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah Donasi (Rp)')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status Pembayaran')
                    ->options([
                        'pending' => 'Pending (Belum Upload Bukti)',
                        'pending_verification' => 'Menunggu Verifikasi Bukti',
                        'success' => 'Sukses (Terverifikasi)',
                        'failed' => 'Gagal / Ditolak',
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\FileUpload::make('proof_of_payment')
                    ->label('Bukti Transfer')
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->directory('bukti-transfer'),
            ]);
    }

    public static function table(Table $table): Table
    {
        // =========================================================================
        // AWAL LOGIKA TOMBOL WHATSAPP DINAMIS
        // =========================================================================
        $waActions = [];
        
        // Memastikan tabel whatsapp_templates sudah ada agar tidak error
        if (Schema::hasTable('whatsapp_templates')) {
            $templates = WhatsappTemplate::all();
            
            foreach ($templates as $template) {
                $waActions[] = Tables\Actions\Action::make('wa_template_' . $template->id)
                    ->label($template->label)
                    ->icon('heroicon-m-chat-bubble-oval-left')
                    ->button()
                    // LOGIKA WARNA: Berubah hijau jika ID template ada di memori donasi
                    ->color(function (Donation $record) use ($template) {
                        $sentIds = $record->wa_sent_ids ?? [];
                        return in_array($template->id, $sentIds) ? 'success' : 'gray';
                    })
                    ->action(function (Donation $record, \Livewire\Component $livewire) use ($template) {
                        // 1. Simpan rekaman klik ke database agar warna tombol berubah
                        $sentIds = $record->wa_sent_ids ?? [];
                        if (!in_array($template->id, $sentIds)) {
                            $sentIds[] = $template->id;
                            $record->update(['wa_sent_ids' => $sentIds]);
                        }

                        // 2. Ganti variabel {nama} dan {nominal} pada teks pesan
                        $pesan = str_replace(
                            ['{nama}', '{nominal}'],
                            [$record->donor_name, number_format($record->amount, 0, ',', '.')],
                            $template->message
                        );

                        // 3. Format nomor HP menjadi 62...
                        $phone = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', (string)$record->phone));
                        
                        // 4. Buka URL WhatsApp di tab baru menggunakan perintah JavaScript
                        $url = 'https://wa.me/' . $phone . '?text=' . urlencode($pesan);
                        $livewire->js("window.open('{$url}', '_blank');");
                    });
            }
        }
        // =========================================================================
        // AKHIR LOGIKA TOMBOL WHATSAPP DINAMIS
        // =========================================================================

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('campaign.title')
                    ->label('Program Donasi')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('donor_name')
                    ->label('Nama Donatur')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('donor_email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('WhatsApp')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode Bayar')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('payment_gateway')
                    ->label('Gateway')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah (Rp)')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float)$state, 0, ',', '.'))
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Belum Upload',
                        'pending_verification' => 'Perlu Verifikasi Bukti',
                        'success' => 'Sukses',
                        'failed' => 'Gagal',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'pending_verification' => 'warning',
                        'success' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(),
                Tables\Columns\ImageColumn::make('proof_of_payment')
                    ->label('Bukti')
                    ->disk('public')
                    ->square()
                    ->url(fn (Donation $record): ?string => $record->proof_of_payment ? Storage::url($record->proof_of_payment) : null, true)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Transaksi')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            // Kita gabungkan Tombol WA yang dinamis dengan tombol Setujui Cepat & tombol bawaan (Invoice, Edit, Hapus)
            ->actions(array_merge([
                Tables\Actions\Action::make('verifikasi_sukses')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->button()
                    ->visible(fn (Donation $record): bool => $record->status !== 'success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui & Verifikasi Donasi Ini?')
                    ->modalDescription('Status donasi akan diubah menjadi Sukses dan dana terkumpul pada program terkait akan otomatis bertambah.')
                    ->action(function (Donation $record): void {
                        $record->update(['status' => 'success']);
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Donasi berhasil diverifikasi!')
                            ->send();
                    }),
            ], $waActions, [
                Tables\Actions\Action::make('cetak_invoice')
                    ->label('Invoice')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn (Donation $record): string => route('payment.receipt', $record->id))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make(),
                
                Tables\Actions\DeleteAction::make()
                    ->after(function (Donation $record) {
                        if ($record->proof_of_payment) {
                            Storage::disk('public')->delete($record->proof_of_payment);
                        }
                    }),
            ]))
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100]);
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
            'index' => Pages\ListDonations::route('/'),
            'create' => Pages\CreateDonation::route('/create'),
            'edit' => Pages\EditDonation::route('/{record}/edit'),
        ];
    }
}