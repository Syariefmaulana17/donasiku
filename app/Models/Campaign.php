<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    // Brankas $fillable sekarang ada di DALAM class
    protected $fillable = [
        'allow_manual_nominal',
        'min_manual_nominal',
        'nominal_choices',
        'progress_display_type',
        'fb_pixel_id',
        'fb_access_token',
        'fb_test_event_code',
        'title', 
        'slug', 
        'description', 
        'target_amount',
        'status',
        'rejection_reason',
        'user_id',
        'cover_image',
        'category',
        'end_date',
        'location',
        'gmaps_url',
        'is_package_enabled',
        'packages',
        'is_min_donation_enabled',
        'min_donation_amount',
    ];
    protected $casts = [
        'nominal_choices' => 'array',
        'allow_manual_nominal' => 'boolean',
    ];

    /**
     * Konversi tipe data otomatis (Casting)
     */
    protected function casts(): array
    {
        return [
            'packages' => 'array',
            'is_package_enabled' => 'boolean',
            'is_min_donation_enabled' => 'boolean',
            'end_date' => 'date',
        ];
    }
    

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }
    public function updates(): HasMany
    {
        return $this->hasMany(CampaignUpdate::class);
    }
    public function withdrawals()
    {
        // Sesuaikan 'Withdrawal::class' jika nama model pencairan Anda berbeda
        return $this->hasMany(\App\Models\Withdrawal::class); 
    }
}