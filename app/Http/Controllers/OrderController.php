<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\User;
use App\Models\Order;
use App\Models\Reward;
use App\Models\Topping;
use App\Models\Category;
use App\Models\Customer;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Events\OrderStatusUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\DiscountController;
use App\Notifications\StockAlertNotification;
use Illuminate\Support\Facades\Auth; // <-- TAMBAHKAN IMPORT INI

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index()
    {
        // Bisa tampilkan semua order
        $orders = Order::with('orderItems.menu')->get();
        return view('orders.index', compact('orders'));
    }

    /**
     * Show popular menus based on total sold quantity.
     */
    public function popularMenus()
    {
        // Ambil menu populer berdasarkan jumlah dibeli
        $popularItems = Menu::select('menus.*', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('order_items', 'menus.id', '=', 'order_items.menu_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'done')
            ->groupBy('menus.id')
            ->orderByDesc('total_sold')
            ->take(6)
            ->get();

        return view('customer.popular_menus', compact('popularItems'));
    }

    public function transactionHistory(Request $request)
    {
        // Mulai query builder untuk Order
        // 'with' digunakan untuk Eager Loading relasi (lebih efisien)
        $query = Order::with(['user', 'orderItems.menu'])
                      ->whereIn('status', ['done', 'complete']); // Hanya ambil transaksi yang selesai


        // --- FILTER LOGIC ---

        // 1. Filter Periode (Default: Hari Ini)
        $selectedDate = $request->input('period', today()->format('Y-m-d'));
        if ($selectedDate) {
            $query->whereDate('created_at', $selectedDate);
        }

        // 2. Filter Metode Pembayaran
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // 3. Filter Pencarian (by Order Code atau Nama Customer)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%");
            });
        }

        // Ambil data setelah difilter, urutkan dari yang terbaru
        $orders = $query->latest()->get();

        // --- PENGHITUNGAN KARTU RINGKASAN ---
        // Catatan: Ini menghitung total berdasarkan hasil filter

        $totalPendapatan = $orders->sum('total_price');
        $totalTransaksi = $orders->count();
        $totalTunai = $orders->where('payment_method', 'cash')->sum('total_price');
        $totalQris = $orders->where('payment_method', 'qris')->sum('total_price');

        // Kirim data ke view
        return view('kasir.riwayat', [
            'orders' => $orders,
            'totalPendapatan' => $totalPendapatan,
            'totalTransaksi' => $totalTransaksi,
            'totalTunai' => $totalTunai,
            'totalQris' => $totalQris,
            'filters' => $request->only(['period', 'payment_method', 'search']) // Untuk mengisi ulang form filter
        ]);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'topping_ids' => 'nullable|array', // Topping sekarang adalah array
            'topping_ids.*' => 'exists:toppings,id', // Validasi setiap ID topping
        ]);

        $menu = Menu::findOrFail($request->menu_id);
        $cart = session()->get('cart', []);

        // Ambil data Topping
        $toppings = Topping::whereIn('id', $request->topping_ids ?? [])->get();
        $toppingsPrice = $toppings->sum('price');
        $toppingsNames = $toppings->pluck('name')->implode(', ');

        // Hitung harga total item
        $itemPrice = $menu->price + $toppingsPrice;

        // ==========================================================
        // KUNCI BARU: Buat ID unik berdasarkan menu & topping
        // Urutkan ID topping agar [1, 2] sama dengan [2, 1]
        $toppingIdsSorted = $request->topping_ids ?? [];
        sort($toppingIdsSorted);
        $cartKey = $menu->id . '-' . implode('-', $toppingIdsSorted);
        // Contoh: '5-1-3' (Menu ID 5, Topping ID 1 dan 3)
        // ==========================================================

        if (isset($cart[$cartKey])) {
            // Jika menu DAN topping yang sama persis sudah ada, tambahkan quantity
            $cart[$cartKey]['quantity']++;
            $cart[$cartKey]['subtotal'] = $cart[$cartKey]['quantity'] * $cart[$cartKey]['price'];
        } else {
            // Jika item baru, tambahkan ke keranjang
            $cart[$cartKey] = [
                'id'       => $cartKey, // Gunakan ID unik
                'menu_id'  => $menu->id,
                'name'     => $menu->name,
                'price'    => $itemPrice, // Harga (Menu + Topping)
                'quantity' => 1,
                'subtotal' => $itemPrice,
                'image'    => $menu->image_url,
                'toppings' => $toppingsNames, // String nama topping (e.g., "Boba, Jelly")
            ];
        }

        session()->put('cart', $cart);
        return response()->json(['success' => true]);
    }

    // METHOD BARU: Untuk mengambil data keranjang sebagai JSON
   public function getCartJson()
    {
        $cart = session()->get('cart', []);
        $total = collect($cart)->sum('subtotal');
        return response()->json([
            'items' => array_values($cart),
            'total' => $total,
            'count' => count($cart), // Hitung jumlah item unik (termasuk topping)
        ]);
    }

    // METHOD BARU: Untuk update quantity
    public function updateQuantity(Request $request, $cartKey) // $id sekarang adalah $cartKey
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$cartKey])) { // Gunakan cartKey
            $cart[$cartKey]['quantity'] = $request->quantity;
            $cart[$cartKey]['subtotal'] = $cart[$cartKey]['quantity'] * $cart[$cartKey]['price'];
            session()->put('cart', $cart);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function viewCart()
    {
        $cart = session()->get('cart', []);
        return view('customer.cart', compact('cart'));
    }

    // ===================================================================
    // INI ADALAH METHOD BARU PENGGANTI 'checkout'
    // Didesain untuk menerima AJAX dari modal
    // ===================================================================
   public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name'  => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'table_number'   => 'nullable|string|max:10',
            'notes'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['message' => 'Keranjang Anda kosong.'], 400);
        }

        DB::beginTransaction();
        try {
            $subtotal = collect($cart)->sum('subtotal');

            $customer = null;
            $discountPercentage = 0.00;
        $discountAmount = 0.00;

            if (Auth::check()) {
                $customer = Auth::user()->customer;
            }

            if ($customer && $customer->is_member) {
                $discountController = new DiscountController();
                $discountPercentage = $discountController->calculateDiscountPercentage($customer);
                $discountAmount = round($subtotal * ($discountPercentage / 100));
            }

            $total_price_final = $subtotal - $discountAmount;

            $order = Order::create([
                'customer_id'    => $customer ? $customer->id : null,
                'customer_name'  => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'table_number'   => $request->table_number,
                'notes'          => $request->notes,
                'order_code'     => 'ORD-' . time() . rand(10, 99),
                'subtotal'       => $subtotal,
                'discount_percentage' => $discountPercentage,
                'discount_amount' => $discountAmount,
                'total_price'    => $total_price_final,
                'payment_method' => null,
                'status'         => 'new',
                'order_type'     => 'online',
            ]);

            foreach ($cart as $cartKey => $item) {

                $menu = Menu::find($item['menu_id']);

                if (!$menu) {
                    throw new \Exception("Menu ID {$item['menu_id']} tidak ditemukan.");
                }

                if ($menu->stock < $item['quantity']) {
                    throw new \Exception("Stok menu '{$menu->name}' tidak mencukupi (Sisa: {$menu->stock}).");
                }

                // Kurangi Stok
                $menu->decrement('stock', $item['quantity']);

                // 🔔 NOTIFIKASI JIKA STOK HABIS (0) 🔔
                if ($menu->fresh()->stock <= 0) {
                    $recipients = User::whereIn('role', ['admin', 'kasir'])->get();
                    foreach ($recipients as $recipient) {
                        $recipient->notify(new StockAlertNotification($menu));
                    }
                }

                $order->orderItems()->create([
                    'menu_id'  => $item['menu_id'],
                    'quantity' => $item['quantity'],
                    'price'    => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'toppings' => $item['toppings'] ?? null
                ]);
            }

            session()->forget('cart');
            DB::commit();

            session(['tracking_order_id' => $order->id]);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat.',
                'order'   => $order
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Checkout Error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updatePayment(Request $request, Order $order)
    {
        // Validasi input (cash atau qris)
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:cash,qris',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Update order yang ada (via Route-Model Binding)
            // Update order yang ada
            $order->update([
                'payment_method' => $request->payment_method,
                'status' => 'new', // <--- GANTI 'paid' MENJADI 'done'
            ]);

            // =========================================================
            // SISTEM POIN MEMBER (START) - Ditambahkan di sini
            // =========================================================

            // Cek apakah order punya customer_id (artinya member login saat checkout)
            if ($order->customer_id) {
                $customer = Customer::find($order->customer_id);

                // Cek apakah data customer valid dan dia member aktif
                if ($customer && $customer->is_member) {

                    // RUMUS POIN: Setiap kelipatan 10.000 dapat 10 poin
                    // Contoh: 25.000 / 10.000 = 2.5 -> floor jadi 2 -> 2 * 10 = 20 Poin
                    $pointsEarned = floor($order->total_price / 10000) * 10;

                    if ($pointsEarned > 0) {
                        $customer->points = $customer->points + $pointsEarned;
                        $customer->save(); // Simpan perubahan poin

                        // Debugging (Opsional): Cek di log laravel.log
                        Log::info("Poin ditambahkan: {$pointsEarned} ke Customer ID: {$customer->id}");
                    }
                }
            }
            // =========================================================
            // SISTEM POIN MEMBER (END)
            // =========================================================

            session(['tracking_order_id' => $order->id]);

            // Menyiapkan "Flash Message" untuk halaman tracking nanti
            session()->flash('order_success', 'Pembayaran Anda berhasil dikonfirmasi!');

            broadcast(new OrderStatusUpdated($order));

            // Kirim respon sukses
            return response()->json([
                'success' => true,
                'message' => 'Metode pembayaran berhasil diperbarui.'
            ]);

        } catch (\Exception $e) {
            Log::error('Update Payment Error: ' . $e->getMessage());

            return response()->json(['message' => 'Gagal memperbarui metode pembayaran.'], 500);
        }
    }

    public function showTrackingPage(Order $order)
    {
        // Validasi: Apakah pelanggan ini berhak melihat order ini?
        // (Cek berdasarkan session yang kita simpan sebelumnya)
        if ((int) session('tracking_order_id') !== (int) $order->id) {
            // Jika tidak, tolak akses
            abort(403, 'Akses Ditolak');
        }

        // Muat relasi yang diperlukan
        $order->load('orderItems.menu');

        // Tampilkan view-nya
        return view('customer.partials.order_tracking', compact('order'));
    }

    /**
     * Tampilkan halaman sukses setelah checkout.
     * Ini adalah halaman tujuan dari 'redirect_url'
     */
    public function success(Order $order)
    {
        // Anda bisa custom halaman ini
        return view('customer.order_success', compact('order'));
    }
    // ===================================================================
    // AKHIR DARI METHOD BARU
    // ===================================================================


    public function remove($cartKey) // $id sekarang adalah $cartKey
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$cartKey])) { // Gunakan cartKey
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function showInputPage()
{
    // 1. Ambil data menu dan kategori
    $menus = Menu::orderBy('name', 'asc')->get();
    $categories = Category::all();

    // 2. WAJIB TAMBAHKAN INI (Ambil data Topping)
    $toppings = Topping::all();

    $rewards = collect();
    $isMember = false;

    // ... (LOGIKA MEMBER & REWARD TETAP SAMA) ...
    if (Auth::check()) {
        $customer = Auth::user()->customer;
        if ($customer && $customer->is_member) {
            $isMember = true;
            $rewards = Reward::get();
        }
    }

    // Tampilkan view 'kasir.input' dan kirim datanya
    return view('kasir.input', [
        'menus' => $menus,
        'categories' => $categories,

        // 3. JANGAN LUPA MASUKKAN KE SINI
        'toppings' => $toppings,

        'rewards' => $rewards,
        'isMember' => $isMember,
    ]);
}

   public function showPaymentPage(Request $request)
    {
        $items = [];
        $total_price_from_form = 0; // Ini adalah subtotal dari form sebelumnya

        if ($request->isMethod('POST')) {
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.menu_id' => 'required|exists:menus,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric',
                'total_price' => 'required|numeric|min:0',
                'customer_id' => 'nullable|exists:customers,id', // ⬅️ TAMBAHAN VALIDASI CUSTOMER ID
            ]);
            $items = $validated['items'];
            $total_price_from_form = $validated['total_price'];
            $customerId = $validated['customer_id'] ?? null;
        } elseif ($request->isMethod('GET') && session()->hasOldInput()) {
            $oldInput = $request->old();

            if (empty($oldInput['items']) || empty($oldInput['total_price'])) {
                // 🚨 PERBAIKAN: Ubah 'kasir.input' ke 'kasir.orders.createManual'
                return redirect()->route('kasir.orders.createManual')
                    ->with('error', 'Keranjang Anda kedaluwarsa. Silakan ulangi.');
            }

            $items = $oldInput['items'];
            $total_price_from_form = $oldInput['total_price'];
            $customerId = $oldInput['customer_id'] ?? null;
        } else {
            // 🚨 PERBAIKAN: Ubah 'kasir.input' ke 'kasir.orders.createManual'
            return redirect()->route('kasir.orders.createManual')
                ->with('error', 'Silakan pilih item terlebih dahulu.');
        }

        // Hitung ulang subtotal di backend untuk keamanan
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Pastikan total dari form = subtotal (keamanan)
        if ($total_price_from_form != $subtotal) {
            // 🚨 PERBAIKAN: Ubah 'kasir.input' ke 'kasir.orders.createManual'
            return redirect()->route('kasir.orders.createManual')
                ->with('error', 'Terjadi kesalahan perhitungan total. Silakan coba lagi.');
        }

        // ⬇️ LOGIKA DISKON DI SINI ⬇️
        $discountPercentage = 0.00;
        $discountAmount = 0.00;
        $customer = null;

        if ($customerId) {
            $customer = Customer::find($customerId);
            if ($customer && $customer->is_member) {
                $discountController = new DiscountController();
                $discountPercentage = $discountController->calculateDiscountPercentage($customer);
                $discountAmount = round($subtotal * ($discountPercentage / 100));
            }
        }

        $totalFinal = $subtotal - $discountAmount; // Total Akhir adalah Subtotal dikurangi Diskon
        // ⬆️ END LOGIKA DISKON ⬆️


        // Kirim data ke view pembayaran
        return view('kasir.pembayaran', [
            'items' => $items,
            'subtotal' => $subtotal,
            'total' => $totalFinal, // Mengirim Total Akhir setelah diskon
            'customer' => $customer, // Kirim data customer ke view
            'discount_amount' => $discountAmount, // Kirim nilai diskon ke view
            'discount_percentage' => $discountPercentage, // Kirim persentase ke view
            'customer_id' => $customerId, // Kirim ID customer untuk proses selanjutnya
        ]);
    }

    /**
     * MEMPROSES akhir pembayaran dari 'pembayaran.blade.php'.
     * Menyimpan order ke database.
     */
 public function processManualPayment(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
            // Validasi topping (array nullable)
        'items.*.toppings' => 'nullable|array',
        'items.*.toppings.*' => 'exists:toppings,id',
            'subtotal' => 'required|numeric|min:0',
            'discount_percentage' => 'required|numeric|min:0',
            'discount_amount' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,qris',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'uang_diterima' => 'nullable|numeric',
            'kembalian' => 'nullable|numeric',
        ]);

        $subtotal_check = 0;
        foreach ($validated['items'] as $item) {
            $subtotal_check += $item['price'] * $item['quantity'];
        }

        $calculated_final_total = round($validated['subtotal'] - $validated['discount_amount']);

        if (round($subtotal_check) != round($validated['subtotal']) || $calculated_final_total != round($validated['total_price'])) {
            Log::warning('Payment fraud detected. Subtotal mismatch.');
            return redirect()->route('kasir.orders.createManual')
                ->with('error', 'Kesalahan harga terdeteksi. Silakan ulangi pesanan.');
        }

        DB::beginTransaction();
        try {
            // 1. LOGIKA PERHITUNGAN KEMBALIAN (TAMBAHAN BARU)
    // Jika metode cash, ambil input 'uang_diterima'. Jika null, default ke total_price.
    $uangDiterima = $validated['uang_diterima'] ?? $validated['total_price'];

    // Jika metode bukan cash (misal QRIS), uang diterima PASTI pas (sama dengan total)
    if ($validated['payment_method'] !== 'cash') {
        $uangDiterima = $validated['total_price'];
    }

    // Hitung kembalian secara manual di backend agar akurat
    $kembalianHitung = $uangDiterima - $validated['total_price'];

    // Pastikan kembalian tidak minus
    if ($kembalianHitung < 0) {
        $kembalianHitung = 0;
    }

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_code' => 'KSR-' . time() . rand(10, 99),
                'customer_id' => $validated['customer_id'] ?? null,
                'subtotal' => $validated['subtotal'],
                'discount_percentage' => $validated['discount_percentage'],
                'discount_amount' => $validated['discount_amount'],
                'total_price' => $validated['total_price'],
                'payment_method' => $validated['payment_method'],
                'status' => 'done',
                'order_type' => 'offline',
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'uang_diterima' => $uangDiterima,
        'kembalian' => $kembalianHitung,
                'table_number' => null,
            ]);

            // 3. Simpan Order Items & KURANGI STOK
            foreach ($validated['items'] as $item) {

                $menu = Menu::find($item['menu_id']);

                if ($menu->stock < $item['quantity']) {
                    throw new \Exception("Stok menu '{$menu->name}' tidak mencukupi (Sisa: {$menu->stock}).");
                }

                // Kurangi Stok
                $menu->decrement('stock', $item['quantity']);

                // 🔔 NOTIFIKASI JIKA STOK HABIS (0) 🔔
                if ($menu->fresh()->stock <= 0) {
                    $recipients = User::whereIn('role', ['admin', 'kasir'])->get();
                    foreach ($recipients as $recipient) {
                        $recipient->notify(new StockAlertNotification($menu));
                    }
                }

                $subtotal = $item['price'] * $item['quantity'];
                $newOrderItem = $order->orderItems()->create([
        'menu_id' => $item['menu_id'],
        'quantity' => $item['quantity'],
        'price' => $item['price'],
        'subtotal' => $subtotal,
    ]);
                // BARU: SIMPAN TOPPING KE TABEL PIVOT
            if (isset($item['toppings']) && is_array($item['toppings'])) {
        foreach ($item['toppings'] as $toppingId) {
            $topping = Topping::find($toppingId); // Pastikan path Model benar

            if($topping) {
                DB::table('order_item_toppings')->insert([
                    // [BUG FIXED] Gunakan ID dari item yang baru dibuat ($newOrderItem->id), BUKAN $order->id
                    'order_item_id' => $newOrderItem->id,

                    'topping_id' => $toppingId,
                    'price' => $topping->price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
            }

            $customer = $order->customer;
            if ($customer && $customer->is_member) {
                $pointsEarned = floor($order->total_price / 1000);
                $customer->points += $pointsEarned;
                $customer->save();
            }

            DB::commit();

            return redirect()->route('kasir.orders.createManual')
                ->with('success', 'Pembayaran berhasil! Order ' . $order->order_code . ' telah dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memproses pembayaran manual: ' . $e->getMessage());
            return redirect()->route('kasir.orders.createManual')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Di Controller Anda (misal: OrderController atau KasirController)
// public function createManual()
// {
//     $menus = Menu::all();
//     $categories = Category::all();

//     // TAMBAHAN: Ambil data topping
//     $toppings = Topping::all();

//     return view('kasir.input', compact('menus', 'categories', 'toppings'));
// }

    private function getOrdersData()
    {
        // 1. Menunggu Konfirmasi (Status: new)
        // Diurutkan dari yang paling baru masuk (created_at desc)
        $newOrders = Order::where('status', 'new')
                        ->where('order_type', 'online')
                        ->orderBy('created_at', 'desc')
                        ->get();

        // 2. Sedang Diproses (Status: process)
        // Diurutkan dari yang paling lama antri (FIFO - First In First Out)
        $processingOrders = Order::where('status', 'process')
                        ->where('order_type', 'online')
                        ->orderBy('updated_at', 'asc')
                        ->get();

        // 3. Siap Diambil / Selesai (Status: done)
        // Diurutkan dari yang baru saja selesai
        $readyOrders = Order::where('status', 'done')
                        ->where('order_type', 'online')
                        ->orderBy('updated_at', 'desc')
                        ->get();

        return compact('newOrders', 'processingOrders', 'readyOrders');
    }

    /**
     * Halaman Utama Dashboard Pesanan Online (Route: /kasir/pesanan-online)
     * Memuat layout lengkap + data awal.
     */
    public function onlineDashboard()
    {
        $data = $this->getOrdersData();
        return view('kasir.online', $data);
    }

    /**
     * Method Khusus AJAX Auto Refresh (Route: /kasir/pesanan-online/refresh)
     * Hanya mengembalikan potongan HTML (Partial View) untuk performa ringan.
     */
    public function refreshOnlineOrders()
    {
        $data = $this->getOrdersData();
        return view('customer.partials._online_orders_grid', $data);
    }

    /**
     * Mengubah Status Pesanan (Digunakan oleh tombol di Kanban Board)
     * Route: POST /kasir/pesanan-online/{order}/update-status
     */
    public function updateStatus(Request $request, Order $order)
{
    $request->validate([
        'status' => 'required|in:new,process,done,cancel,complete'
    ]);

    $order->update([
        'status' => $request->status
    ]);

    // Broadcast event agar Tracking Customer terupdate otomatis
    broadcast(new OrderStatusUpdated($order));

    return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
}

}
