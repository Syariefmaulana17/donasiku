<?php

namespace App\Livewire;

use App\Models\Campaign;
use App\Models\Donation;
use Livewire\Component;

class CheckoutDonation extends Component
{
    public $campaign;
    public $otherCampaigns; 
    public $amount = 50000; 
    public $name = '';
    public $is_anonymous = false;
    public $prayer = '';
    public $payment_method = '';
    
    // 1. Tambahkan dua variabel baru ini
    public $phone = '';
    public $email = '';

    public function mount($slug)
    {
        $this->campaign = Campaign::where('slug', $slug)->firstOrFail();
        
        $this->otherCampaigns = Campaign::where('id', '!=', $this->campaign->id)
                                        ->inRandomOrder()
                                        ->take(3)
                                        ->get();
    }

    public function setAmount($value)
    {
        $this->amount = $value;
    }

    public function processPayment()
    {
        // 2. Tambahkan validasi untuk phone (wajib) dan email (opsional)
        $this->validate([
            'amount' => 'required|numeric|min:1000',
            'phone' => 'required|string|min:9|max:15',
            'payment_method' => 'required|string',
            'phone' => 'required|numeric', // Nomor telepon wajib diisi dan harus berupa angka
            'email' => 'nullable|email'    // Email boleh kosong (opsional), tapi jika diisi harus berformat email
        ], [
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'email.email' => 'Format email tidak valid.'
        ]);

        $donorName = $this->name;
        if ($this->is_anonymous || empty(trim($this->name))) {
            $donorName = 'Hamba Allah';
        }

        // 3. Simpan data phone dan email ke database
        $donation = Donation::create([
            'campaign_id' => $this->campaign->id,
            'donor_name'  => $donorName,
            'phone' => $this->phone,
            'donor_phone' => $this->phone, // Menyimpan nomor telepon
            'donor_email' => $this->email, // Menyimpan email
            'amount'      => $this->amount,
            'status'      => 'pending',
            'payment_method'  => $this->payment_method, // Menangkap pilihan donatur (QRIS/VA)
            'payment_gateway' => 'Manual / Transfer',
            // 'prayer' => $this->prayer,
        ]);

        return redirect()->route('payment.instruction', [
            'id' => $donation->id,
            'method' => $this->payment_method
        ]);
    }

    public function render()
    {
        return view('livewire.checkout-donation')->layout('components.layouts.app');
    }
}