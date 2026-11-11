<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'donor_name',
        'donor_email',
        'phone',
        'payment_method',
        'payment_gateway',
        'amount',
        'status',
        'proof_of_payment',
        'wa_sent_ids'
    ];
    
    protected $casts = [
        'wa_sent_ids' => 'array',
        // ... kode cast Anda yang lain jika ada
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    // --- AKUNTAN OTOMATIS MULAI BEKERJA DI SINI ---
    protected static function booted(): void
    {
        $recalculateCampaignTotal = function (self $donation): void {
            $campaignIds = array_unique(array_filter([
                $donation->campaign_id,
                $donation->getOriginal('campaign_id'),
            ]));

            foreach ($campaignIds as $campaignId) {
                $totalTerkumpul = self::where('campaign_id', $campaignId)
                    ->where('status', 'success')
                    ->sum('amount');

                Campaign::where('id', $campaignId)->update([
                    'current_amount' => $totalTerkumpul,
                ]);
            }
        };

        // Setiap kali data donasi baru dibuat ATAU diedit/disimpan
        static::saved($recalculateCampaignTotal);

        // Setiap kali data donasi dihapus
        static::deleted($recalculateCampaignTotal);
    }
}