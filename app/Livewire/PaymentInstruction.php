<?php

namespace App\Livewire;

use App\Models\Donation;
use App\Models\HomepageSetting;
use Carbon\Carbon;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

class PaymentInstruction extends Component
{
    use WithFileUploads;

    #[Url]
    public $id;

    #[Url]
    public $amount;

    #[Url]
    public $method;

    public $donation;
    public $deadline_time;
    public $deadline_formatted;
    public $bank_name = 'Transfer Bank';
    public $bank_logo = null;
    public $va_number = '-';
    public $account_name = 'Yayasan DonasiKu';
    public $proof_of_payment;
    public $is_success = false;

    public function mount($id = null, $method = null)
    {
        if ($id !== null) {
            $this->id = $id;
        }
        if ($method !== null) {
            $this->method = $method;
        }

        if (! $this->id) {
            return redirect('/');
        }

        $this->donation = Donation::with('campaign')->find($this->id);
        if (! $this->donation) {
            return redirect('/');
        }

        if ($this->donation->status === 'success') {
            return redirect()->route('payment.success', $this->donation->id);
        }

        if (empty($this->method)) {
            $this->method = $this->donation->payment_method ?: 'Transfer Bank';
        }

        $this->amount = $this->donation->amount;

        // Jika sebelumnya sudah pernah mengunggah bukti transfer
        if (! empty($this->donation->proof_of_payment) || $this->donation->status === 'pending_verification') {
            $this->is_success = true;
        }

        // Ambil daftar rekening resmi dari pengaturan Admin (ManageFooter)
        $homeSetting = HomepageSetting::first();
        $siteName = $homeSetting->site_name ?? 'DonasiKu';
        $this->account_name = $siteName;
        $this->bank_name = strtoupper(str_replace('va_', '', (string) $this->method));

        $matchedBank = null;
        $banks = ($homeSetting && ! empty($homeSetting->footer_banks) && is_array($homeSetting->footer_banks))
            ? $homeSetting->footer_banks
            : [];

        $searchKey = strtolower(trim(str_replace('va_', '', (string) $this->method)));

        foreach ($banks as $bank) {
            $bankNameLower = strtolower(trim((string) ($bank['bank_name'] ?? '')));
            if ($bankNameLower !== '' && ($bankNameLower === $searchKey || str_contains($bankNameLower, $searchKey) || str_contains($searchKey, $bankNameLower))) {
                $matchedBank = $bank;
                break;
            }
        }

        if (! $matchedBank && count($banks) > 0) {
            $matchedBank = $banks[0];
        }

        if ($matchedBank) {
            $this->bank_name = $matchedBank['bank_name'] ?? $this->bank_name;
            $this->bank_logo = $matchedBank['logo'] ?? null;
            $this->va_number = $matchedBank['account_number'] ?? '-';
            $this->account_name = $matchedBank['account_name'] ?? $siteName;
        } else {
            // Fallback jika Admin belum mengisi rekening di menu Footer
            if (str_contains($searchKey, 'bca')) {
                $this->va_number = '88000' . str_pad((string) $this->donation->id, 6, '0', STR_PAD_LEFT);
            } elseif (str_contains($searchKey, 'mandiri')) {
                $this->va_number = '89022' . str_pad((string) $this->donation->id, 6, '0', STR_PAD_LEFT);
            } else {
                $this->va_number = '190088' . str_pad((string) $this->donation->id, 6, '0', STR_PAD_LEFT);
            }
        }

        $deadline = ($this->donation->created_at ?? Carbon::now())->copy()->addHours(24);
        if ($deadline->isPast()) {
            $deadline = Carbon::now()->addHours(24);
        }
        $this->deadline_time = $deadline->timestamp * 1000;
        $this->deadline_formatted = $deadline->translatedFormat('l, d F Y H:i') . ' WIB';
    }

    public function submitProof(): void
    {
        $this->validate([
            'proof_of_payment' => 'required|image|max:4096',
        ], [
            'proof_of_payment.required' => 'Harap pilih foto bukti transfer terlebih dahulu.',
            'proof_of_payment.image' => 'File harus berupa gambar (JPG, PNG, WEBP).',
            'proof_of_payment.max' => 'Ukuran gambar maksimal 4 MB.',
        ]);

        $path = $this->proof_of_payment->store('bukti-transfer', 'public');

        $this->donation->update([
            'proof_of_payment' => $path,
            'status' => 'pending_verification',
        ]);

        $this->is_success = true;
    }

    public function checkPaymentStatus()
    {
        if (! $this->donation) {
            return;
        }

        $this->donation->refresh();

        if ($this->donation->status === 'success') {
            return redirect()->route('payment.success', $this->donation->id);
        }
    }

    public function render()
    {
        return view('livewire.payment-instruction')->layout('components.layouts.app', [
            'title' => 'Instruksi Pembayaran Donasi',
        ]);
    }
}
