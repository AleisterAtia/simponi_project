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
        Schema::table('rewards', function (Blueprint $table) {
            // Menambahkan kolom menu_id
            // nullable()      : Agar bisa kosong (untuk reward barang fisik)
            // constrained()   : Otomatis menghubungkan ke id di tabel 'menus'
            // nullOnDelete()  : Jika menu dihapus, kolom ini jadi NULL (reward tidak error/hilang)

            $table->foreignId('menu_id')
                  ->nullable()
                  ->after('id') // Meletakkan kolom setelah 'id'
                  ->constrained('menus')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('rewards', function (Blueprint $table) {
            // Hapus foreign key dulu, baru kolomnya
            $table->dropForeign(['menu_id']);
            $table->dropColumn('menu_id');
        });
    }
};
