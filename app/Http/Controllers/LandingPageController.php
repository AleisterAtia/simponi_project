<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Reward; // ⬅️ Model Reward
use App\Models\OrderItem; // Untuk Popular Menu
use Illuminate\Support\Facades\DB;


class LandingPageController extends Controller
{
    public function index()
    {
        // 1. Ambil data menu dan kategori (data utama)
        $menus = Menu::orderBy('name', 'asc')->get();
        $categories = Category::all();

        // 2. LOGIKA POPULAR MENU
        $popularMenus = OrderItem::select('menu_id')
            ->selectRaw('SUM(quantity) as total_sold')
            ->groupBy('menu_id')
            ->orderByDesc('total_sold')
            ->take(6)
            ->with('menu')
            ->get()
            ->map(fn($item) => $item->menu)
            ->filter();

        // 3. LOGIKA KONDISIONAL MEMBER DAN REWARD
        $rewards = collect(); // Default: Collection kosong
        $isMember = false;

        if (Auth::check()) {
            // ASUMSI: Relasi Auth::user()->customer sudah ada dan benar
            $customer = Auth::user()->customer; 
            
            // Cek apakah user memiliki data customer DAN berstatus member
            if ($customer && $customer->is_member) {
                $isMember = true;
                
                // 🚨 PERBAIKAN KRITIS DI SINI (Menghapus with('menu')):
                // Karena Model Reward tidak punya relasi menu, kita ambil datanya langsung.
                $rewards = Reward::get(); 
            }
        }
        
        // 4. Render view dan KIRIM SEMUA DATA
        return view('customer.landingpage', [
            'menus' => $menus,
            'categories' => $categories,
            'popularMenus' => $popularMenus, 
            'rewards' => $rewards,     
            'isMember' => $isMember,   
        ]);
    }
}