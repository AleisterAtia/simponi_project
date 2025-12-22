<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',      // admin/kasir/customer
        'shift_id',  // relasi ke shift
        'is_active', // status aktif/tidak
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Shift
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Relasi ke Customer (One-to-One)
     * Setiap akun User dengan role 'customer' memiliki satu data member Customer.
     */
    public function customer()
    {
        // Mencari entri di tabel 'customers' yang user_id-nya cocok dengan id User ini
        return $this->hasOne(Customer::class);
    }
}