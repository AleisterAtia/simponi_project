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
        // 1. Tentukan Rentang Tanggal (Date Range Logic)

        // Default: Bulan Ini
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $label = 'Bulan Ini';
        $tab = $request->input('tab', 'bulanan'); // Default tab

        // A. Cek Filter Manual (Input Tanggal) - Prioritas Tertinggi
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $label = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
            $tab = 'custom'; // Tandai sebagai custom agar tab lain tidak aktif
        }
        // B. Cek Tab Shortcut
        elseif ($tab == 'harian') {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
            $label = 'Hari Ini (' . $startDate->format('d M Y') . ')';
        }
        elseif ($tab == 'mingguan') {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
            $label = 'Minggu Ini';
        }

        // Hitung selisih hari untuk rata-rata (hindari pembagian 0)
        $daysInPeriod = max(1, $startDate->diffInDays($endDate) + 1);


        // 2. Query Utama (Ringkasan)
        // Ambil order yang statusnya 'done' dalam rentang tanggal
        $ordersQuery = Order::whereIn('status', ['done', 'complete'])
           ->whereBetween('created_at', [$startDate, $endDate]);


        $totalPendapatan = $ordersQuery->sum('total_price'); // Sum total harga
        $totalTransaksi = $ordersQuery->count(); // Hitung jumlah baris
        $rataRataHarian = $totalPendapatan / $daysInPeriod;


        // 3. Menu Terlaris (FIX ERROR $pendapatan)
        // Kita perlu join tabel untuk mendapatkan nama menu dan menghitung subtotal item
        $menuTerlaris = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('menus', 'menus.id', '=', 'order_items.menu_id')
            ->whereIn('orders.status', ['done', 'complete'])
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'menus.name',
                DB::raw('SUM(order_items.quantity) as total_terjual'),
                DB::raw('SUM(order_items.subtotal) as pendapatan') // <--- INI SOLUSI ERRORNYA
            )
            ->groupBy('menus.id', 'menus.name')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();


        // 4. Distribusi Penjualan (Kategori)
        // Menghitung berapa item terjual per kategori
        $kategoriStats = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('menus', 'menus.id', '=', 'order_items.menu_id')
            ->join('categories', 'categories.id', '=', 'menus.category_id') // Asumsi ada tabel categories
            ->whereIn('orders.status', ['done', 'complete'])
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'categories.name',
                DB::raw('SUM(order_items.quantity) as total_item')
            )
            ->groupBy('categories.id', 'categories.name')
            ->get();

        // Hitung Persentase & Formatting Data Kategori
        $totalItemTerjual = $kategoriStats->sum('total_item');
        $distribusiPenjualan = $kategoriStats->map(function ($item) use ($totalItemTerjual) {
            return [
                'name' => $item->name,
                'total' => $item->total_item,
                'percentage' => $totalItemTerjual > 0 ? ($item->total_item / $totalItemTerjual) * 100 : 0,
                'color' => $this->getCategoryColor($item->name) // Helper warna
            ];
        });


        // 5. Kirim Data ke View
        return view('admin.laporan', [
            'tab' => $tab,
            'tanggalLabel' => $label,
            'totalPendapatan' => $totalPendapatan,
            'totalTransaksi' => $totalTransaksi,
            'rataRataHarian' => $rataRataHarian,
            'menuTerlaris' => $menuTerlaris,
            'distribusiPenjualan' => $distribusiPenjualan,
        ]);
    }

    // Helper sederhana untuk warna kategori (Bisa disesuaikan)
    private function getCategoryColor($categoryName)
    {
        $colors = [
            'bg-orange-500',
            'bg-blue-500',
            'bg-green-500',
            'bg-yellow-400',
            'bg-purple-500',
            'bg-pink-500',
            'bg-indigo-500'
        ];

        // Pilih warna acak berdasarkan panjang nama agar konsisten
        return $colors[strlen($categoryName) % count($colors)] ?? 'bg-gray-400';
    }
}
