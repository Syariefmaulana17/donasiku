<?php

namespace App\Livewire;

use App\Models\Campaign;
use Livewire\Component;

class CampaignSearch extends Component
{
    public $title = 'Program Donasi Pilihan';
    public $subtitle = 'Mari berbagi kebaikan hari ini untuk mereka yang membutuhkan.';
    public $category = 'semua';
    public $selectedCategory = 'semua';
    public $search = '';
    public $showFilter = true;

    public function mount($title = null, $subtitle = null, $category = 'semua', $showFilter = true): void
    {
        if ($title !== null) {
            $this->title = $title;
        }
        if ($subtitle !== null) {
            $this->subtitle = $subtitle;
        }
        $this->category = $category ?: 'semua';
        $this->selectedCategory = $this->category;
        $this->showFilter = $showFilter;
    }

    public function selectCategory(string $cat): void
    {
        $this->selectedCategory = $cat;
    }

    public function render()
    {
        $query = Campaign::with('user')
            ->withCount(['donations' => fn ($q) => $q->where('status', 'success')])
            ->withSum(['donations' => fn ($q) => $q->where('status', 'success')], 'amount')
            ->where('status', 'active');

        if ($this->selectedCategory && $this->selectedCategory !== 'semua') {
            $query->where('category', $this->selectedCategory);
        }

        if (trim($this->search) !== '') {
            $keyword = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', $keyword)
                  ->orWhere('location', 'like', $keyword)
                  ->orWhere('category', 'like', $keyword);
            });
        }

        $campaigns = $query->latest()->get();

        return view('livewire.campaign-search', [
            'campaigns' => $campaigns,
        ]);
    }
}
