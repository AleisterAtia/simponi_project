<?php

namespace App\Http\Controllers;

use Exception;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Reward;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Events\OrderStatusUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KasirOrderController extends Controller
{

public function createManual()
{
    // 1. DATA UTAMA: MENU & KATEGORI
    // Saya hapus 'select' spesifik agar kolom 'stock' juga terbawa (penting untuk validasi stok)
    $menus = Menu::where('status', 'tersedia')
                ->with('category')
                ->orderBy('name', 'asc')
                ->get();

    // Ambil kategori dari database langsung agar urutannya sesuai ID/Nama
    $categories = Category::all();
    // Atau jika ingin hanya kategori yang punya menu saja (seperti kode Yang Mulia sebelumnya):
    // $categories = $menus->pluck('category')->unique('id')->filter();

    // 2. [WAJIB] DATA TOPPING (Solusi Error Undefined Variable)
    $toppings = \App\Models\Topping::all();

    // 3. LOGIKA MEMBER & REWARD
    $rewards = collect(); // Default kosong (koleksi kosong)
    $isMember = false;

    if (auth()->check()) {
        // Asumsi: User kasir yang login tidak relevan dengan "member customer",
        // TAPI jika maksudnya mengecek user yang sedang login:
        // (Biasanya kasir menginput member_id customer di form, bukan mengambil dari Auth kasir)
        // Namun saya ikuti logika function showInputPage yang Yang Mulia kirim sebelumnya:

        $user = auth()->user();

        // Cek apakah user ini punya data customer terhubung
        if ($user->customer && $user->customer->is_member) {
            $isMember = true;
            $rewards = Reward::all();
        }
    }

    // 4. KIRIM SEMUA DATA KE VIEW
    return view('kasir.input', compact(
        'menus',
        'categories',
        'toppings',   // <-- Ini yang bikin error sebelumnya
        'rewards',    // <-- Permintaan tambahan
        'isMember'    // <-- Permintaan tambahan
    ));
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

        $today = Carbon::today();
        // 1. Hitung Pendapatan Harian (Hanya yang statusnya 'done' atau 'completed')
        $dailyRevenue = Order::with('orderItems.menu')
                        ->whereIn('status', ['done', 'complete'])
                        ->whereDate('created_at', $today)
                        ->sum('total_price');

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
                            ->whereIn('status', ['done', 'complete'])
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
    $request->validate([
        'status' => 'required|in:new,process,done,cancel,complete'
    ]);

    $order->update([
        'status' => $request->status
    ]);

    broadcast(new OrderStatusUpdated($order));

    return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
}

}
