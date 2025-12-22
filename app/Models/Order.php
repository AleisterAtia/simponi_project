<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'order_code',
        'total_price', // Total Akhir
        'payment_method',
        'status',
        'note',
        'order_type',
        'user_id',
        'customer_name',
        'customer_phone',
        'table_number',
        'notes',
        
        // ⬇️ TIGA KOLOM BARU WAJIB DITAMBAH DI SINI ⬇️
        'subtotal',             // Subtotal Awal (Basis Diskon)
        'discount_percentage',  // Total Persen Diskon
        'discount_amount',      // Nilai Rupiah Diskon
    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }

    public function payment() {
        return $this->hasOne(Payment::class);
    }
}