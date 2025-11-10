<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Redemption extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id','reward_id','points_used','redemption_date'];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function reward() {
        return $this->belongsTo(Reward::class);
    }
}
