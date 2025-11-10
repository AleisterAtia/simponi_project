<?php

use App\Models\Order;
use Illuminate\Support\Carbon;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsKasir;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ToppingController;
use App\Http\Controllers\KasirOrderController;
use App\Http\Controllers\LandingPageController;
/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingPageController::class, 'index'])->name('landingpage');

/*
|--------------------------------------------------------------------------
| Admin Dashboard & Menu CRUD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
// MENJADI INI:
Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // CRUD Menu
    Route::resource('menu', MenuController::class);
    Route::post('menu/{menu}/toggle-status', [MenuController::class, 'toggleStatus'])->name('menu.toggleStatus');


    // Contoh route tambahan admin lain bisa ditambahkan di sini
    // Route::get('/settings', function() { ... })->name('settings');
});

// 1. Halaman untuk "Input Pesanan"
    Route::get('/kasir/input-pesanan', [KasirOrderController::class, 'createManual'])
         ->name('kasir.orders.createManual');

    // 3. Aksi untuk menyimpan pesanan manual
    Route::post('/kasir/input-pesanan', [KasirOrderController::class, 'storeManual'])
         ->name('kasir.orders.storeManual');

/*
|--------------------------------------------------------------------------
| Kasir Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/kasir/riwayat-transaksi', [OrderController::class, 'transactionHistory'])
    ->name('kasir.riwayat');


Route::middleware(['auth', IsKasir::class])->group(function () {
    Route::get('/kasir', function () {

        // Ambil data untuk hari ini
        $today = Carbon::today();

        // 1. Hitung Pendapatan Harian (dari order yang statusnya 'done' atau 'cancel')
        $dailyRevenue = Order::whereIn('status', ['done', 'cancel'])
                             ->whereDate('created_at', $today)
                             ->sum('total_price');

        // 2. Hitung Pesanan Baru (status 'new')
        $newOrderCount = Order::where('status', 'new')
                              ->whereDate('created_at', $today)
                              ->count();

        // 3. Hitung Sedang Diproses (status 'process')
        $processingOrderCount = Order::where('status', 'process')
                                     ->whereDate('created_at', $today)
                                     ->count();

        // 4. Hitung Selesai Hari Ini (status 'done' atau 'cancel')
        $doneOrderCount = Order::whereIn('status', ['done', 'cancel'])
                               ->whereDate('created_at', $today)
                               ->count();

        // Kirim semua data ini ke view
        return view('kasir.index', [
            'dailyRevenue'         => $dailyRevenue,
            'newOrderCount'        => $newOrderCount,
            'processingOrderCount' => $processingOrderCount,
            'doneOrderCount'       => $doneOrderCount,
        ]);

    })->name('kasir.dashboard');

    Route::get('/kasir/pesanan-online', [KasirOrderController::class, 'index'])
         ->name('kasir.orders.online');

    // 2. Aksi untuk mengubah status pesanan
    Route::post('/kasir/pesanan/{order}/update-status', [KasirOrderController::class, 'updateStatus'])
         ->name('kasir.orders.updateStatus');
});

/*
|--------------------------------------------------------------------------
| Profile Routes (semua user login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Keranjang
Route::post('/cart/add', [OrderController::class, 'addToCart'])->name('cart.add');
Route::delete('/cart/remove/{id}', [OrderController::class, 'remove'])->name('cart.remove');
Route::get('/cart/json', [OrderController::class, 'getCartJson'])->name('cart.json');
Route::patch('/cart/update/{cartKey}', [OrderController::class, 'updateQuantity'])
    ->name('cart.update')
    ->where('cartKey', '.*'); // Izinkan karakter '.' dan '-'

Route::delete('/cart/remove/{cartKey}', [OrderController::class, 'remove'])
    ->name('cart.remove')
    ->where('cartKey', '.*'); // Izinkan karakter '.' dan '-'

Route::get('/cart', [OrderController::class, 'viewCart'])->name('cart.view');
Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
// Tambahkan juga route untuk halaman sukses
Route::get('/order/success/{order}', [OrderController::class, 'success'])->name('order.success');
// Route::delete('/cart/remove/{id}', [OrderController::class, 'remove'])->name('cart.remove');



Route::patch('/order/{order}/payment', [OrderController::class, 'updatePayment'])->name('order.payment.update');
Route::get('/toppings/json', [ToppingController::class, 'getJson'])->name('toppings.json');

// Route untuk halaman sukses (jika diperlukan, misal untuk tracking)
Route::get('/order/success/{order}', [OrderController::class, 'success'])->name('order.success');

// Route untuk halaman tracking real-time
Route::get('/track/{order}', [OrderController::class, 'showTrackingPage'])->name('order.track');

// Laporan
Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports');

// Inputan Manual Kasir
// 1. Rute POST untuk MENAMPILKAN halaman pembayaran
//    Rute ini menerima data keranjang dari form 'input.blade.php'
Route::match(['GET', 'POST'], '/kasir/pembayaran', [OrderController::class, 'showPaymentPage'])
    ->name('kasir.pembayaran.show');

// 2. Rute POST untuk MEMPROSES pembayaran
//    Rute ini menerima data dari form 'pembayaran.blade.php'
Route::post('/kasir/pembayaran/proses', [OrderController::class, 'processManualPayment'])
    ->name('kasir.pembayaran.process');

// Route::get('/kasir/input-pesanan', [OrderController::class, 'showInputPage'])
//     ->name('kasir.input');

// Auth routes (login, register, logout, dll)
require __DIR__.'/auth.php';
