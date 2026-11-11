<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    // Mengizinkan kolom-kolom ini untuk diisi data oleh sistem
    protected $fillable = [
        'campaign_id',
        'amount',
        'purpose',
        'platform_fee',
        'bank_name',
        'account_number',
        'account_name',
        'status',
        'rejection_reason',
        'transfer_proof',
    ];

    // Relasi: Menjelaskan bahwa penarikan ini milik sebuah Campaign
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}