<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    // 1. Definisikan Nama Tabel (Opsional jika nama tabel jamak dari nama model)
    protected $table = 'discounts';

    // 2. Definisikan Kolom yang Boleh Diisi (Mass Assignment)
    protected $fillable = [
        'nama_diskon',
        'tipe_diskon', // Kunci logika: 'TETAP', 'ULANG_TAHUN', 'SPESIAL'
        'persentase_nilai',
        'tanggal_mulai',
        'tanggal_akhir',
        'status',
    ];

    // 3. Konversi Tipe Data (Casting)
    // Pastikan persentase_nilai selalu berupa angka/float
    protected $casts = [
        'persentase_nilai' => 'float',
        'tanggal_mulai'    => 'date',
        'tanggal_akhir'    => 'date',
    ];

    // 4. Query Scope untuk Akses Data Cepat (Penting!)
    
    /**
     * Scope untuk memfilter diskon berdasarkan tipe diskon.
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $tipe  Contoh: 'TETAP', 'ULANG_TAHUN', 'SPESIAL'
     * @return void
     */
    public function scopeOfType($query, $tipe) 
    {
        return $query->where('tipe_diskon', $tipe);
    }

    /**
     * Scope untuk memfilter diskon yang berstatus 'Aktif'.
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeActive($query) 
    {
        return $query->where('status', 'Aktif');
    }
    
    // TIDAK PERLU relasi hasMany/belongsTo di sini
    // karena diskon diakses secara statis/logis.
}