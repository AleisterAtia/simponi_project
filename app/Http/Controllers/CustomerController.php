<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
// Jangan lupa import Model Menu jika Anda ingin menampilkan data dari database
// use App\Models\Menu; 

class CustomerController extends Controller
{
    /**
     * Menampilkan halaman landing page customer.
     * Menggunakan method 'landingPage' sesuai saran sebelumnya.
     */
    public function landingPage()
    {
        // Secara opsional, Anda bisa mengambil data menu dari database
        // $menus = Menu::where('is_popular', true)->limit(3)->get();
        // $allMenus = Menu::all();

        // Mengembalikan view yang telah Anda buat
        return view('customer.landingpage'
            // , compact('menus', 'allMenus') // Jika Anda menggunakan data dinamis
        );
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // ... (fungsi resource index lainnya)
    }

    // ... (method create, store, show, edit, update, destroy lainnya)
}