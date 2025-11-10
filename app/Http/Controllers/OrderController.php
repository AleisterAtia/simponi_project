<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Topping;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Events\OrderStatusUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator; // <-- TAMBAHKAN IMPORT INI

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
                      ->where('status', 'done'); // Hanya ambil transaksi yang selesai


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
        // 1. Validasi input dari modal
        $validator = Validator::make($request->all(), [
            'customer_name'  => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'table_number'   => 'nullable|string|max:10',
            'notes'          => 'nullable|string',
        ]);

        // 2. Jika validasi gagal
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 3. Ambil keranjang dari session
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json(['message' => 'Keranjang Anda kosong.'], 400);
        }

        DB::beginTransaction();
        try {
            // PERUBAHAN: Hitung subtotal dan total dengan pajak 10% di backend
            $subtotal = collect($cart)->sum('subtotal');
            $total_price_with_tax = $subtotal * 1.10; // Asumsi pajak 10%

            // 4. Buat order
            $order = Order::create([
                // === Data dari Modal Guest Checkout ===
                'customer_name'  => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'table_number'   => $request->table_number,
                'notes'          => $request->notes,

                // === Info Order (Disesuaikan dengan DB Anda) ===
                'order_code'     => 'ORD-' . time() . rand(10, 99),
                'total_price'    => $total_price_with_tax, // <-- PERUBAHAN: Total harga + pajak
                'payment_method' => null, // <-- PERUBAHAN: Default null, akan diisi di modal 3
                'status'         => 'new',  // <-- PERUBAHAN: Sesuai skema DB Anda
                'order_type'     => 'online', // <-- PERUBAHAN: Sesuai skema DB Anda
            ]);

            // 5. Buat order item
foreach ($cart as $cartKey => $item) { // <--- $menuId diubah namanya jadi $cartKey
    $order->orderItems()->create([
        'menu_id'  => $item['menu_id'], // <--- AMBIL DARI SINI
        'quantity' => $item['quantity'],
        'price'    => $item['price'],
        'subtotal' => $item['subtotal']
        // 'toppings' => $item['toppings']
    ]);
}
    // Anda juga bisa menyimpan topping di sini jika ada kolomnya

            // 6. Bersihkan session keranjang
            session()->forget('cart');
            DB::commit();


            session(['tracking_order_id' => $order->id]);

            // 7. PERUBAHAN UTAMA: Kirim respon SUKSES sebagai JSON
            // Kirim objek 'order' agar bisa ditangkap Alpine.js
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat, silakan pilih pembayaran.',
                'order'   => $order // <-- Kirim data order ke frontend
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Checkout Error: ' . $e->getMessage()); // Catat error
            return response()->json(['message' => 'Terjadi kesalahan saat memproses pesanan.'], 500);
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
            $order->update([
                'payment_method' => $request->payment_method
            ]);

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
        // Ambil data menu dan kategori untuk ditampilkan di halaman input
        // Kita juga perlu $menus dan $categories untuk view 'input.blade.php'
        $menus = Menu::orderBy('name', 'asc')->get();
        $categories = Category::all();

        // Tampilkan view 'kasir.input' dan kirim datanya
        return view('kasir.input', [
            'menus' => $menus,
            'categories' => $categories
        ]);
    }

   public function showPaymentPage(Request $request)
    {
        $items = [];
        $total_price_from_form = 0; // Ini adalah subtotal dari form sebelumnya

        if ($request->isMethod('POST')) {
            // ALUR NORMAL: Datang dari 'input.blade.php'
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.menu_id' => 'required|exists:menus,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric',
                'total_price' => 'required|numeric|min:0'
            ]);
            $items = $validated['items'];
            $total_price_from_form = $validated['total_price'];

        } elseif ($request->isMethod('GET') && session()->hasOldInput()) {
            // ALUR GAGAL VALIDASI: Datang dari redirect()->back()
            $oldInput = $request->old(); // Ambil input lama (termasuk error)

            if (empty($oldInput['items']) || empty($oldInput['total_price'])) {
                // Jika data lama tidak ada, lempar kembali ke input
                return redirect()->route('kasir.input')
                    ->with('error', 'Keranjang Anda kedaluwarsa. Silakan ulangi.');
            }

            $items = $oldInput['items'];
            $total_price_from_form = $oldInput['total_price'];

        } else {
            // ALUR AKSES LANGSUNG: User mengetik URL /kasir/pembayaran
            return redirect()->route('kasir.input')
                ->with('error', 'Silakan pilih item terlebih dahulu.');
        }

        // --- Lanjutkan dengan logika yang sama ---

        // Hitung ulang subtotal di backend untuk keamanan
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Pastikan total dari form = subtotal (keamanan)
        // Jika $total_price_from_form tidak sama dengan $subtotal, ada manipulasi data
        if ($total_price_from_form != $subtotal) {
             return redirect()->route('kasir.input')
                ->with('error', 'Terjadi kesalahan perhitungan total. Silakan coba lagi.');
        }

        $pajak = $subtotal * 0.10; // Pajak 10%
        $totalDenganPajak = $subtotal + $pajak;

        // Kirim data ke view pembayaran
        return view('kasir.pembayaran', [
            'items' => $items,
            'subtotal' => $subtotal,
            'pajak' => $pajak,
            'total' => $totalDenganPajak
        ]);
    }

    /**
     * MEMPROSES akhir pembayaran dari 'pembayaran.blade.php'.
     * Menyimpan order ke database.
     */
public function processManualPayment(Request $request)
    {
        // 1. Validasi (TAMBAHKAN customer_name & customer_phone)
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
            'total_price' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,qris',

            // ⬇️ TAMBAHAN VALIDASI BARU ⬇️
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',

            'uang_diterima' => 'nullable|numeric',
            'kembalian' => 'nullable|numeric',
        ]);

        DB::beginTransaction();
        try {
            // 2. Buat Order (GUNAKAN data pelanggan dari validasi)
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_code' => 'KSR-' . time() . rand(10, 99),
                'total_price' => $validated['total_price'],
                'payment_method' => $validated['payment_method'],
                'status' => 'done',
                'order_type' => 'offline',

                // ⬇️ PERUBAHAN LOGIKA ⬇️
                'customer_name' => $validated['customer_name'], // Gunakan data dari form
                'customer_phone' => $validated['customer_phone'], // Gunakan data dari form

                'table_number' => null,
            ]);

            // 3. Simpan Order Items (Tidak ada perubahan di sini)
            foreach ($validated['items'] as $item) {
                // ... (kode Anda untuk 'orderItems' sudah benar)
                $menu = Menu::find($item['menu_id']);
                $subtotal = $item['price'] * $item['quantity'];

                $order->orderItems()->create([
                    'menu_id' => $item['menu_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);
            }

            DB::commit();

            // 4. Redirect (PERBAIKAN BUG)
            // ⬇️ GANTI 'kasir.orders.createManual' MENJADI 'kasir.input' ⬇️
            return redirect()->route('kasir.orders.createManual')
            ->with('success', 'Pembayaran berhasil! Order ' . $order->order_code . ' telah dibuat.');

        } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Gagal memproses pembayaran manual: ' . $e->getMessage());

        // 5. Redirect (PERBAIKAN BUG)
        return redirect()->route('kasir.orders.createManual')
            ->with('error', 'Terjadi kesalahan. Pembayaran gagal diproses.'); // <-- INI BENAR

    }
    }
}
