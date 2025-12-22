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
        Schema::table('orders', function (Blueprint $table) {
            // Kolom untuk basis harga sebelum diskon
            $table->decimal('subtotal', 10, 2)->after('order_code'); 
            
            // Kolom untuk mencatat hasil diskon
            $table->decimal('discount_percentage', 5, 2)->default(0.00)->after('subtotal');
            $table->decimal('discount_amount', 10, 2)->default(0.00)->after('discount_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('subtotal');
            $table->dropColumn('discount_percentage');
            $table->dropColumn('discount_amount');
        });
    }
};