<?php

namespace App\Livewire;

use App\Models\Campaign;
use Livewire\Component;

class CampaignDetail extends Component
{
    public $campaign;
    public $otherCampaigns;

    public function mount($slug): void
    {
        $this->campaign = Campaign::with([
            'user',
            'updates' => fn ($q) => $q->latest(),
            'donations' => fn ($q) => $q->where('status', 'success')->latest(),
            'withdrawals' => fn ($q) => $q->where('status', 'completed')->latest(),
        ])->where('slug', $slug)->firstOrFail();

        $this->otherCampaigns = Campaign::where('id', '!=', $this->campaign->id)
            ->where('status', 'active')
            ->inRandomOrder()
            ->take(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.campaign-detail')
            ->layout('components.layouts.app', [
                'title' => $this->campaign->title . ' - DonasiKu',
            ]);
    }
}
