<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        // Ambil semua menu
        $menus = Menu::all();

        // Ambil kategori
        $categories = Category::all();

        // Ambil menu populer berdasarkan jumlah dibeli (order_items)
        $popularMenus = OrderItem::select('menu_id')
            ->selectRaw('SUM(quantity) as total_sold')
            ->groupBy('menu_id')
            ->orderByDesc('total_sold')
            ->take(6) // maksimal 6 menu populer
            ->with('menu')
            ->get()
            ->map(function($orderItem) {
                return $orderItem->menu;
            })
            ->filter(); // hilangkan null kalau menu dihapus

        return view('customer.landingpage', [
            'menus' => $menus,
            'categories' => $categories,
            'popularMenus' => $popularMenus,
        ]);
    }
}
