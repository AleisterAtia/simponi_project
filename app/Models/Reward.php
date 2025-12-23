<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Menghapus import Menu karena relasi menu() dihapus.
// use App\Models\Menu; 

class Reward extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        // 'menu_id', // DIHILANGKAN: Tidak ada di DB Anda saat ini
        'points_required',
        'stock',
        // 'description', // DIHILANGKAN: Tidak ada di DB Anda saat ini
    ];

    /**
     * RELASI 'MENU' DIHILANGKAN SEMENTARA.
     * * PERINGATAN KRITIS: Karena tabel rewards tidak memiliki kolom 'menu_id',
     * relasi menu() harus dihapus agar tidak terjadi RelationNotFoundException.
     *
     * Anda harus memperbaiki View (landingpage.blade.php) untuk tidak mengakses
     * $reward->menu->* karena data menu tidak lagi terhubung.
     */
    // public function menu()
    // {
    //     return $this->belongsTo(Menu::class);
    // }
    
    // Asumsi: Model Redemption sudah ada
    public function redemptions() 
    {
        return $this->hasMany(Redemption::class);
    }
}