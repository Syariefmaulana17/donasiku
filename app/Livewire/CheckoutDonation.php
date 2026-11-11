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
    public $phone = '';
    public $email = '';

    public function mount($slug): void
    {
        $this->campaign = Campaign::where('slug', $slug)->firstOrFail();

        if (! empty($this->campaign->nominal_choices) && is_array($this->campaign->nominal_choices)) {
            $firstNominal = (int) ($this->campaign->nominal_choices[0]['nominal'] ?? 50000);
            if ($firstNominal > 0) {
                $this->amount = $firstNominal;
            }
        }

        $this->otherCampaigns = Campaign::where('id', '!=', $this->campaign->id)
            ->where('status', 'active')
            ->inRandomOrder()
            ->take(3)
            ->get();
    }

    public function setAmount($value): void
    {
        $this->amount = (int) $value;
    }

    public function processPayment()
    {
        $minAmount = 1000;
        if ($this->campaign->is_min_donation_enabled && $this->campaign->min_donation_amount > 0) {
            $minAmount = (int) $this->campaign->min_donation_amount;
        } elseif ($this->campaign->min_manual_nominal > 0) {
            $minAmount = (int) $this->campaign->min_manual_nominal;
        }

        $this->validate([
            'amount' => 'required|numeric|min:' . $minAmount,
            'payment_method' => 'required|string',
            'phone' => 'required|numeric|digits_between:9,15',
            'email' => 'nullable|email',
        ], [
            'amount.min' => 'Nominal donasi minimal Rp ' . number_format($minAmount, 0, ',', '.') . '.',
            'phone.required' => 'Nomor WhatsApp/telepon wajib diisi.',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.digits_between' => 'Nomor telepon harus terdiri dari 9 hingga 15 angka.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $donorName = $this->name;
        if ($this->is_anonymous || empty(trim($this->name))) {
            $donorName = 'Hamba Allah';
        }

        $donation = Donation::create([
            'campaign_id' => $this->campaign->id,
            'donor_name' => $donorName,
            'phone' => $this->phone,
            'donor_email' => $this->email,
            'amount' => $this->amount,
            'status' => 'pending',
            'payment_method' => $this->payment_method,
            'payment_gateway' => 'Manual / Transfer',
        ]);

        return redirect()->route('payment.instruction', [
            'id' => $donation->id,
            'method' => $this->payment_method,
        ]);
    }

    public function render()
    {
        return view('livewire.checkout-donation')->layout('components.layouts.app', [
            'title' => 'Donasi: ' . $this->campaign->title,
        ]);
    }
}
