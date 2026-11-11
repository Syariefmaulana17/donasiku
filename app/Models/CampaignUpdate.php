<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'title',
        'content',
    ];

    // Relasi kembali ke Campaign
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}