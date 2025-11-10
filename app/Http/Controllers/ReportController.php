<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
public function index(Request $request)
{
    $tab = $request->input('tab', 'bulanan');

    // Kita gunakan satu patokan waktu 'sekarang'
    $now = Carbon::now();

    // Default values (gunakan copy() agar $now tidak berubah-ubah)
    $startDate = $now->copy()->startOfMonth();
    $endDate   = $now->copy()->endOfMonth();
    $daysInPeriod = $now->day;

    // Tentukan rentang tanggal berdasarkan tab
    if ($tab === 'harian') {
        $startDate = $now->copy()->startOfDay();
        $endDate   = $now->copy()->endOfDay();
        $daysInPeriod = 1;
    } elseif ($tab === 'mingguan') {
        $startDate = $now->copy()->startOfWeek();
        $endDate   = $now->copy()->endOfWeek();
        $daysInPeriod = 7;
    } elseif ($tab === 'bulanan') {
        // Paksa set ulang agar yakin
        $startDate = $now->copy()->startOfMonth();
        $endDate   = $now->copy()->endOfMonth();
        $daysInPeriod = $now->day;
    }

    // --- DEBUG AREA ---
    // Jika masih error, uncomment baris di bawah ini untuk melihat tanggal pastinya.
    // Pastikan startDate dan endDate BERBEDA.
    // dd($startDate->toDateTimeString(), $endDate->toDateTimeString());
    // ------------------

    // 1. Query dasar
    $ordersQuery = Order::where('status', 'done')
        ->whereBetween('created_at', [$startDate, $endDate]);

    // 2. Hitung Ringkasan (TETAP GUNAKAN CLONE)
    $totalPendapatan = $ordersQuery->clone()->sum('total_price');
    $totalTransaksi  = $ordersQuery->clone()->count();

    // Hitung rata-rata (hindari pembagian dengan nol)
    $rataRataHarian = ($totalTransaksi > 0 && $daysInPeriod > 0)
                        ? $totalPendapatan / $daysInPeriod
                        : 0;

    // 3. Menu Terlaris
    $menuTerlaris = DB::table('order_items')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('menus', 'order_items.menu_id', '=', 'menus.id')
        ->where('orders.status', 'done')
        ->whereBetween('orders.created_at', [$startDate, $endDate])
        ->select('menus.name', DB::raw('SUM(order_items.quantity) as total_terjual'))
        ->groupBy('menus.name')
        ->orderByDesc('total_terjual')
        ->limit(5)
        ->get();

    // 4. Distribusi Penjualan (KOSONGKAN DULU sementara debugging)
    $distribusiPenjualan = collect();

    return view('admin.laporan', [
        'tab' => $tab,
        'totalPendapatan' => $totalPendapatan,
        'totalTransaksi' => $totalTransaksi,
        'rataRataHarian' => $rataRataHarian,
        'menuTerlaris' => $menuTerlaris,
        'distribusiPenjualan' => $distribusiPenjualan,
        'tanggalLabel' => $this->getTanggalLabel($tab, $startDate, $endDate)
    ]);
}

// Update sedikit helper tanggalnya agar lebih akurat untuk mingguan
private function getTanggalLabel($tab, $startDate, $endDate = null) {
    if ($tab === 'harian') return $startDate->format('d F Y');
    if ($tab === 'mingguan') return $startDate->format('d M') . ' - ' . $endDate->format('d M Y');
    return $startDate->format('F Y');
}

    // Helper untuk warna chart (sesuaikan dengan kategori Anda)
    private function getCategoryColor($categoryName) {
        switch (strtolower($categoryName)) {
            case 'coffee': return 'bg-yellow-800'; // Coklat tua
            case 'matcha': return 'bg-green-700'; // Hijau
            case 'milk':   return 'bg-gray-700';   // Hitam/Abu tua
            case 'tea':    return 'bg-orange-400'; // Oranye
            default:       return 'bg-gray-400';
        }
    }
}
