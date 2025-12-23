<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Untuk keperluan tanggal

// ⬇️ TAMBAHAN: Impor Event Broadcast Anda
use App\Events\PaymentConfirmed; 
use App\Events\OrderStatusUpdated; 

class PaymentController extends Controller
{
    /**
     * Menampilkan struk pembayaran.
     * Menggunakan Route Model Binding untuk Order, atau mencari Order berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        // 1. Cari Order
        $order = Order::findOrFail($id);
        
        // ⬇️ VARIABEL UNTUK MEMBANDINGKAN STATUS SEBELUM DIUBAH ⬇️
        $oldStatus = $order->status;

        // 2. Update Data Order (Status & Metode Pembayaran)
        // **CATATAN PENTING:** // Karena ini adalah PaymentController, kita asumsikan 'new' -> 'paid'
        // Jika Anda ingin menggunakan status 'process' di tracking pelanggan, 
        // Anda harus memilih status mana yang harus diterapkan di sini:
        $newStatus = 'process'; // Kita gunakan 'process' agar tracking berjalan
        
        $order->status = $newStatus; 
        $order->payment_method = $request->payment_method;
        $order->payment_status = 'paid'; // Jika ada kolom payment_status
        $order->save();

        // =========================================================
        // SISTEM POIN MEMBER (START)
        // =========================================================

        // Cek 1: Apakah user login?
        // Cek 2: Apakah user punya data customer?
        if (Auth::check() && Auth::user()->customer) {

            $customer = Auth::user()->customer;

            // Cek 3: Apakah dia Member Aktif?
            // (Asumsi kolom is_member bernilai 1 atau true)
            if ($customer->is_member) {

                // Ambil total belanja
                $totalBelanja = $order->total_price;

                // RUMUS: Setiap kelipatan 10.000 dapat 10 poin
                // floor(25000 / 10000) = 2.  => 2 * 10 = 20 Poin.
                $pointsEarned = floor($totalBelanja / 10000) * 10;

                if ($pointsEarned > 0) {
                    // Tambahkan ke poin yang ada sekarang
                    $customer->points = $customer->points + $pointsEarned;
                    $customer->save();
                }
            }
        }
        // =========================================================
        // SISTEM POIN MEMBER (END)
        // =========================================================


        // =========================================================
        // ⬇️ TAMBAHAN: LOGIKA BROADCAST UNTUK TRACKING PELANGGAN ⬇️
        // =========================================================
        
        // Asumsi: 
        // 1. Jika order sebelumnya 'new' (menunggu bayar) dan sekarang 'process' (dibayar & dikonfirmasi), 
        //    maka kirim event PaymentConfirmed.
        // 2. Jika ini order manual, $request->payment_confirmed akan null/false, jadi broadcast normal.

        // **PERHATIAN**: Karena ini dari PaymentController, kita TIDAK punya hidden input `payment_confirmed`
        // dari sisi Kasir. Kita asumsikan SEMUA update dari method ini adalah konfirmasi pembayaran.
        
        if ($oldStatus === 'new' && $newStatus === 'process') {
            // Ini adalah konfirmasi pembayaran pertama. Kirim event PaymentConfirmed.
            // Pelanggan akan melihat modal "Berhasil Dikonfirmasi" dan tracking berubah ke 'process'.
            broadcast(new PaymentConfirmed($order));
        } else {
            // Jika status berubah dari selain 'new' atau ini order manual/order lama, 
            // kita tetap memancarkan event umum (optional, jika Anda ingin semua update status terkirim)
            // Namun, karena ini PaymentController, seharusnya hanya terjadi sekali (new -> process).
            broadcast(new OrderStatusUpdated($order)); 
        }

        // =========================================================
        // ⬆️ LOGIKA BROADCAST UNTUK TRACKING PELANGGAN (END) ⬆️
        // =========================================================
        

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dikonfirmasi',
            'order_id' => $order->id
        ]);
    }

    /**
     * Menampilkan struk pembayaran.
     */
    public function showReceipt($orderId)
    {
        // Konten showReceipt tidak diubah, untuk menjaga fungsionalitas order manual.
        
        // 1. Ambil Order dan detailnya dengan Eager Loading
        $order = Order::with(['orderItems.menu'])->findOrFail($orderId);

        // 2. Mapping data
        $subtotal = $order->subtotal ?? 0;
        $totalAkhir = $order->total_price ?? $subtotal;
        $uangDiterima = $order->uang_diterima ?? $totalAkhir;
        $kembalian = $order->kembalian ?? 0;
        $paymentMethod = $order->payment_method ?? 'Tunai';

        $data = [
            'order' => $order,
            'orderId' => $order->order_code ?? $order->id, // Fallback ke ID jika order_code null
            'customerName' => $order->customer_name,
            'items' => $order->orderItems,

            // Variabel Harga & Diskon
            'subtotal' => (float) $subtotal,
            'discountAmount' => $order->discount_amount ?? 0,
            'discountPercentage' => $order->discount_percentage ?? 0,
            'totalAkhir' => (float) $totalAkhir,

            // Detail Bayar
            'paymentMethod' => $paymentMethod,
            'uangDiterima' => (float) $uangDiterima,
            'kembalian' => (float) $kembalian,
        ];

        return view('kasir.partials.cetak_struk', $data);
    }
}