<?php

namespace App\Livewire;

use App\Models\Campaign;
use Livewire\Component;

class CampaignUpdates extends Component
{
    public $campaign;

    public function mount($slug)
    {
        $this->campaign = Campaign::with(['updates' => function($q) {
            $q->latest();
        }])->where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.campaign-updates')->layout('components.layouts.app');
    }
}