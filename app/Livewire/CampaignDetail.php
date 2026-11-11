<?php

namespace App\Livewire;

use App\Models\Campaign;
use Livewire\Component;

class CampaignDetail extends Component
{
    public $campaign;
    public $otherCampaigns; // Variabel baru untuk menampung program lain

    public function mount($slug)
    {
        // 1. Ambil data campaign utama
        $this->campaign = Campaign::with([
            'user',
            'updates' => fn($q) => $q->latest(),
            'donations' => fn($q) => $q->latest()
        ])->where('slug', $slug)->firstOrFail();

        // 2. Ambil 3 program donasi lain secara acak (Cross-selling)
        $this->otherCampaigns = Campaign::where('id', '!=', $this->campaign->id)
                                        ->inRandomOrder()
                                        ->take(3)
                                        ->get();
    }

    public function render()
    {
        return view('livewire.campaign-detail')
            ->layout('components.layouts.app'); 
    }
}