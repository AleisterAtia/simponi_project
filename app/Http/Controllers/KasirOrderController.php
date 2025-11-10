<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Events\OrderStatusUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KasirOrderController extends Controller
{

    public function createManual()
    {
// Ambil semua menu yang tersedia
        $menus = Menu::where('status', 'tersedia') // Asumsi ada kolom status
                     ->select('id', 'name', 'price', 'image', 'category_id') // Ambil data yang diperlukan
                     ->with('category:id,name') // Muat relasi kategori
                     ->get();

        // Ambil semua kategori yang memiliki menu
        $categories = $menus->pluck('category')->unique()->filter();

        return view('kasir.input', compact('menus', 'categories'));
    }

    /**
     * [AJAX] Cari menu berdasarkan kode_menu.
     */

    /**
     * Simpan pesanan manual dari kasir.
     */
    /**
     * Simpan pesanan manual dari kasir.
     */
    public function storeManual(Request $request)
    {
        // 1. TAMBAHKAN VALIDASI UNTUK PAYMENT_METHOD
        $data = $request->validate([
            'items'                 => 'required|array|min:1',
            'items.*.menu_id'       => 'required|exists:menus,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.price'         => 'required|numeric',
            'total_price'           => 'required|numeric',
            'payment_method'        => 'required|in:cash,qris', // <-- WAJIB ADA
        ]);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'order_code'     => 'ORD-MANUAL-' . time() . rand(10, 99),
                'total_price'    => $data['total_price'],
                'status'         => 'new', // Status awal
                'order_type'     => 'offline', // Sesuai skema DB Anda
                'user_id'        => auth()->id(), // Kasir yang login

                // 2. MENGATASI ERROR "GAGAL MEMBUAT PESANAN"
                // Tambahkan nilai default untuk kolom yang mungkin wajib diisi
                'customer_name'  => 'Walk-in Customer',
                'customer_phone' => 'N/A', // Atau nomor kasir

                // 3. AMBIL PAYMENT_METHOD DARI MODAL
                'payment_method' => $data['payment_method'],
            ]);

            // Simpan ke order_items
            foreach ($data['items'] as $item) {
                $order->orderItems()->create([
                    'menu_id'  => $item['menu_id'],
                    'quantity' => $item['quantity'],
                    'price'    => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            DB::commit();

            // Arahkan ke halaman "Pembayaran" (sesuai route Anda)
            return redirect()->route('kasir.pembayaran.show', $order)
                             ->with('success', 'Pesanan #' . $order->order_code . ' berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Manual Order Error: ' . $e->getMessage());
            // Kirim pesan error yang lebih jelas ke view
            return back()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }


    public function index()
    {
        // Ambil pesanan yang masih aktif dan muat relasi itemnya
        // Kita gunakan 'order_items.menu' untuk mengakses nama menu
        $newOrders = Order::with('orderItems.menu')
                          ->where('status', 'new')
                          ->orderBy('created_at', 'asc')
                          ->get();

        $processingOrders = Order::with('orderItems.menu')
                                 ->where('status', 'process')
                                 ->orderBy('created_at', 'asc')
                                 ->get();

        $readyOrders = Order::with('orderItems.menu')
                            ->where('status', 'done')
                            ->orderBy('created_at', 'asc')
                            ->get();

        return view('kasir.online', compact(
            'newOrders',
            'processingOrders',
            'readyOrders'
        ));
    }

    /**
     * Mengubah status pesanan.
     * Ini adalah FUNGSI KUNCI yang akan mentrigger update di halaman pelanggan.
     */
    public function updateStatus(Request $request, Order $order)
    {
        // Validasi status baru
        $validated = $request->validate([
            'status' => 'required|in:process,done,cancel',
        ]);

        // 1. Update status pesanan di database
        $order->update([
            'status' => $validated['status']
        ]);

        // 2. SIARKAN (BROADCAST) EVENT UPDATE STATUS
        // Pelanggan yang sedang di halaman tracking akan menerima update ini.
        broadcast(new OrderStatusUpdated($order))->toOthers();

        return back()->with('success', 'Status pesanan #' . $order->order_code . ' berhasil diupdate.');
    }
}
