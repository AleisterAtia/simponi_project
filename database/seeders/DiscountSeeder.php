<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mendapatkan tanggal hari ini (untuk diskon spesial)
        $today = Carbon::today()->toDateString();
        $nextWeek = Carbon::today()->addWeek()->toDateString();

        DB::table('discounts')->insert([
            // 1. DISKON TETAP MEMBER (10%)
            [
                'nama_diskon' => 'Diskon Member Tetap 10%',
                'tipe_diskon' => 'TETAP',
                'persentase_nilai' => 10.00,
                'tanggal_mulai' => null, // Diskon Tetap tidak punya tanggal mulai/akhir
                'tanggal_akhir' => null,
                'status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // 2. DISKON ULANG TAHUN (25% Tambahan)
            [
                'nama_diskon' => 'Diskon Ulang Tahun Member 25%',
                'tipe_diskon' => 'ULANG_TAHUN',
                'persentase_nilai' => 25.00,
                'tanggal_mulai' => null,
                'tanggal_akhir' => null,
                'status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 3. DISKON HARI SPESIAL (Contoh 10% Tambahan)
            [
                'nama_diskon' => 'Promo Awal Tahun 10%',
                'tipe_diskon' => 'SPESIAL',
                'persentase_nilai' => 10.00,
                'tanggal_mulai' => $today, // Berlaku mulai hari ini
                'tanggal_akhir' => $nextWeek, // Berlaku hingga minggu depan
                'status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}