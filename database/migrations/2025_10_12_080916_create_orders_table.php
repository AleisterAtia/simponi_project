<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_code')->unique();
            $table->decimal('total_price', 12, 2);
            $table->enum('payment_method', ['cash', 'qris'])->nullable();
            $table->enum('status', ['new', 'process', 'done', 'cancel'])->default('new');
            $table->text('note')->nullable();
            $table->enum('order_type', ['online', 'offline'])->default('online');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('orders');
    }
};
