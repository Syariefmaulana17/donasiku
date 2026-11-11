<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads; // Wajib dipanggil untuk fitur upload
use Carbon\Carbon;
use App\Models\Donation; // Memanggil tabel database Donasi

class PaymentInstruction extends Component
{
    use WithFileUploads; // Mengaktifkan mesin upload file

    #[Url]
    public $id; // Menangkap ID Donasi dari URL

    #[Url]
    public $amount;

    #[Url]
    public $method;

    public $donation;
    public $deadline_time;
    public $deadline_formatted;
    public $va_number = '1900880015000000'; 
    public $proof_of_payment; // Variabel untuk menyimpan gambar
    public $is_success = false; // Penanda jika upload berhasil

    public function mount()
    {
        // Jika tidak ada ID donasi atau metode, tendang ke beranda
        if (!$this->id || !$this->method) {
            return redirect('/');
        }

        // Cari data donasi berdasarkan ID
        $this->donation = Donation::find($this->id);
        if (!$this->donation) {
            return redirect('/');
        }

        // Kita samakan nominalnya dengan yang ada di database agar lebih aman
        $this->amount = $this->donation->amount;

        // Simulasi nomor rekening dinamis (Opsional: agar terlihat lebih nyata)
        if (str_contains($this->method, 'bca')) {
            $this->va_number = '88000' . $this->donation->id;
        } elseif (str_contains($this->method, 'mandiri')) {
            $this->va_number = '89022' . $this->donation->id;
        }

        // Simulasi batas waktu pembayaran (+15 menit dari sekarang)
        $deadline = Carbon::now()->addMinutes(15);
        $this->deadline_time = $deadline->timestamp * 1000; 
        $this->deadline_formatted = $deadline->translatedFormat('l, d F Y H:i') . ' WIB';
    }

    // Fungsi ini akan berjalan saat tombol "Konfirmasi Pembayaran" diklik
    public function submitProof()
    {
        // 1. Pastikan gambar sudah dipilih dan ukurannya tidak lebih dari 2MB
        $this->validate([
            'proof_of_payment' => 'required|image|max:2048', 
        ], [
            'proof_of_payment.required' => 'Harap pilih foto bukti transfer terlebih dahulu.',
            'proof_of_payment.image' => 'File harus berupa gambar (JPG, PNG).',
            'proof_of_payment.max' => 'Ukuran gambar maksimal 2 MB.'
        ]);

        // 2. Simpan gambar ke folder storage Hostinger Anda
        $path = $this->proof_of_payment->store('bukti-transfer', 'public');

        // 3. Update data donasi di database (masukkan foto & ubah status)
        $this->donation->update([
            'proof_of_payment' => $path,
            'status' => 'pending_verification' // Status berubah menunggu dicek admin
        ]);

        // 4. Ubah penanda sukses menjadi true untuk memunculkan pesan sukses di tampilan
        $this->is_success = true;
    }
    
    public function checkPaymentStatus()
    {
        // Segarkan data donasi dari database
        $this->donation->refresh();

        // Jika Admin mengubah statusnya jadi success, langsung lempar!
        if ($this->donation->status === 'success') {
            return redirect()->route('payment.success', $this->donation->id);
        }
    }

    public function render()
    {
        return view('livewire.payment-instruction')->layout('components.layouts.app');
    }
}