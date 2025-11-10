<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name','phone','member_code','is_member','points'];

    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function pointsHistory() {
        return $this->hasMany(MembershipPointsHistory::class);
    }

    public function redemptions() {
        return $this->hasMany(Redemption::class);
    }
}
