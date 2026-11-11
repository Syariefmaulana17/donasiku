<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 1. KITA PANGGIL FITUR IZIN FILAMENT
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

// 2. TAMBAHKAN IMPLEMENTASI FILAMENTUSER
class User extends Authenticatable implements FilamentUser
{
    // 3. PERBAIKAN: HasApiTokens sudah dihapus dari baris ini
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_approved',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // 4. SURAT IZIN MASUK DASBOR
    public function canAccessPanel(Panel $panel): bool
    {
        return true; 
    }
}