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
    protected static function booted()
    {
        // 'saved' berarti: Setiap kali data donasi baru dibuat ATAU diedit/disimpan
        static::saved(function ($donation) {
            
            // 1. Hitung total semua uang yang statusnya 'success' pada program ini
            $totalTerkumpul = self::where('campaign_id', $donation->campaign_id)
                                  ->where('status', 'success')
                                  ->sum('amount');
            
            // 2. Panggil Manajer Papan Pengumuman (Campaign)
            $campaign = $donation->campaign;
            
            // 3. Update angkanya agar otomatis bertambah di etalase depan!
            if ($campaign) {
                $campaign->current_amount = $totalTerkumpul;
                $campaign->save();
            }
        });
    }
}