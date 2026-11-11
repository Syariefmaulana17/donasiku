<?php

namespace App\Livewire;

use App\Models\Campaign;
use Livewire\Component;

class CampaignDonations extends Component
{
    public $campaign;

    public function mount($slug)
    {
        $this->campaign = Campaign::with(['donations' => function($q) {
            $q->latest();
        }])->where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.campaign-donations')->layout('components.layouts.app');
    }
}