<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'birth_date',
        'member_code',
        'is_member',
        'points'
    ];
    
    // PENTING: Cast birth_date ke tipe date/datetime agar mudah dimanipulasi oleh Carbon
    protected $casts = [
        'birth_date' => 'date', 
    ];

    /**
     * Relasi ke User (One-to-One / Belongs To)
     * Customer belongs to one User account for authentication.
     */

    public function getStatusLabelAttribute()
    {
        $points = $this->points;

        if ($points > 400) {
            return 'Gold Member';
        } elseif ($points > 200) {
            return 'Silver Member';
        } else {
            return 'Bronze Member';
        }
    }

    // 2. Accessor untuk Pesan Upgrade (Berapa poin lagi?)
    public function getNextLevelMessageAttribute()
    {
        $points = $this->points;

        if ($points > 400) {
            return 'Anda di level tertinggi (Sultan!)';
        } elseif ($points > 200) {
            $needed = 401 - $points; // Target Gold di 401
            return "Upgrade ke Gold dengan {$needed} poin lagi";
        } else {
            $needed = 201 - $points; // Target Silver di 201
            return "Upgrade ke Silver dengan {$needed} poin lagi";
        }
    }
    
    // 3. Accessor untuk Warna Text (Opsional, biar cantik)
    public function getStatusColorAttribute()
    {
        $points = $this->points;
        if ($points > 400) return 'text-yellow-500'; // Warna Emas
        if ($points > 200) return 'text-gray-500';   // Warna Perak
        return 'text-orange-600';                    // Warna Perunggu
    }

    public function user() 
    {
        // Mencari User yang ID-nya sesuai dengan user_id di tabel customers
        return $this->belongsTo(User::class, 'user_id'); 
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function pointsHistory() {
        return $this->hasMany(MembershipPointsHistory::class);
    }

    public function redemptions() {
        return $this->hasMany(Redemption::class);
    }

    // ==========================================================
    // HELPER & ACCESSOR UNTUK LOGIKA DISKON & MEMBER STATUS
    // ==========================================================

    /**
     * HELPER: Memeriksa apakah hari ini adalah hari ulang tahun customer.
     * Digunakan oleh DiscountController untuk Diskon Ulang Tahun.
     */
    public function isBirthdayToday(): bool
    {
        // Jika birth_date null, tentu bukan ulang tahun
        if (!$this->birth_date) {
            return false;
        }

        $today = Carbon::now();
        
        // Membandingkan hanya bulan dan tanggal
        return $today->month === $this->birth_date->month
            && $today->day === $this->birth_date->day;
    }

    /**
     * ACCESSOR: Mengambil status member sebagai boolean.
     * Digunakan untuk pengecekan status member di Controller.
     */
    public function getIsMemberAttribute($value)
    {
        // Mengembalikan nilai boolean, karena kolom is_member biasanya TINYINT(1)
        return (bool) $value; 
    }
}