<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function index()
{
    // --- 1. Data untuk Kartu Statistik ---
    $today = Carbon::today();
    $yesterday = Carbon::yesterday();

    // Pendapatan hari ini
    $totalRevenueToday = Order::where('status', 'done')
                            ->whereDate('created_at', $today)
                            ->sum('total_price');

    // Pendapatan kemarin (untuk perbandingan)
    $totalRevenueYesterday = Order::where('status', 'done')
                                ->whereDate('created_at', $yesterday)
                                ->sum('total_price');

    // Hitung persentase perubahan
    if ($totalRevenueYesterday > 0) {
        $revenueChangePercentage = (($totalRevenueToday - $totalRevenueYesterday) / $totalRevenueYesterday) * 100;
    } else {
        $revenueChangePercentage = $totalRevenueToday > 0 ? 100 : 0; // Jika kemarin 0, anggap 100%
    }

    // --- 2. Data untuk Status Pesanan ---
    $newOrdersCount = Order::where('status', 'new')
                           ->whereDate('created_at', $today)
                           ->count();

    $processingOrdersCount = Order::where('status', 'process')
                                  ->whereDate('created_at', $today)
                                  ->count();

    $completedOrdersCount = Order::where('status', 'done')
                                ->whereDate('created_at', $today)
                                ->count();

    // --- 3. Data untuk "Pesanan Terbaru" ---
    $latestOrders = Order::latest()->take(5)->get(); // Ambil 5 pesanan terakhir

    // --- 4. Data untuk "Notifikasi Stok" ---
    // (Asumsi Anda punya kolom 'stok' di model Menu)
    $outOfStockProducts = Menu::where('stock', '<=', 0)->get();


    // --- 5. Kirim semua data ke View ---
    return view('admin.index', [ // Pastikan nama view-nya 'admin.index'
        'totalRevenueToday' => $totalRevenueToday,
        'revenueChangePercentage' => round($revenueChangePercentage, 2), // Bulatkan persentase
        'newOrdersCount' => $newOrdersCount,
        'processingOrdersCount' => $processingOrdersCount,
        'completedOrdersCount' => $completedOrdersCount,
        'latestOrders' => $latestOrders,
        'outOfStockProducts' => $outOfStockProducts, // Ini akan memperbaiki error Anda
    ]);
}

public function settings()
    {
        return view('admin.settings', [
            'user' => auth()->user(),
        ]);
    }
}
