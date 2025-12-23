<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();

            // Kolom Inti Diskon
            $table->string('nama_diskon', 100);
            
            // Kunci Logika untuk Aplikasi (Wajib)
            // Hanya tiga tipe yang diizinkan: TETAP, ULANG_TAHUN, SPESIAL
            $table->enum('tipe_diskon', ['TETAP', 'ULANG_TAHUN', 'SPESIAL']); 
            
            // Besaran diskon dalam persentase (e.g., 10.00, 25.00)
            $table->decimal('persentase_nilai', 5, 2); 

            // Kolom Periode (NULLABLE, hanya diisi untuk tipe SPESIAL)
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_akhir')->nullable();
            
            // Status Diskon
            $table->enum('status', ['Aktif', 'Non-Aktif'])->default('Aktif'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};