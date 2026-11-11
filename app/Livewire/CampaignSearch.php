<?php

namespace App\Livewire;

use App\Models\Campaign;
use Livewire\Component;

class CampaignSearch extends Component
{
    // Variabel yang bisa diatur dari luar
    public $title = 'Program Donasi';
    public $subtitle = 'Mari berbagi kebaikan hari ini.';
    public $category = 'semua';

    public function render()
    {
        // Mencari program yang statusnya 'active' (tayang)
        $query = Campaign::where('status', 'active');

        // Jika kategori bukan 'semua', saring berdasarkan kategori
        if ($this->category !== 'semua') {
            $query->where('category', $this->category);
        }

        // Ambil data terbaru
        $campaigns = $query->latest()->get();

        return view('livewire.campaign-search', [
            'campaigns' => $campaigns
        ]);
    }
}