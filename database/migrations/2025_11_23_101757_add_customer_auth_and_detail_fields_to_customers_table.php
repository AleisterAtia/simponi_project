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
        Schema::table('customers', function (Blueprint $table) {
            // 1. user_id (Relasi ke tabel users untuk autentikasi)
            // Diletakkan setelah 'id'
            // $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            
            // 2. email (Diperlukan untuk form input dan konsistensi data)
            // Dibuat unique karena setiap customer memiliki 1 akun login yang unik.
            // $table->string('email')->unique()->nullable()->after('name');
            
            // 3. birth_date (Tanggal Lahir untuk fitur diskon)
            // Diletakkan setelah 'address'
            //$table->date('birth_date')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('customers', function (Blueprint $table) {
        //     // Hapus foreign key terlebih dahulu
         //    $table->dropForeign(['user_id']);
            
        //     // Hapus semua kolom yang ditambahkan
           //  $table->dropColumn(['user_id', 'email', 'birth_date']);
         });
        Schema::table('customers', function (Blueprint $table) {
            // Hapus hanya kolom yang ditambahkan di UP() ini
            //$table->dropColumn(['birth_date']); 
        });
    }
};