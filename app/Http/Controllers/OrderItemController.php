<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderItemController extends Controller
{
    /**
     * Display a listing of order items.
     */
    public function index()
    {
        $orderItems = OrderItem::with('menu', 'order')->get();
        return view('order_items.index', compact('orderItems'));
    }

    /**
     * Show popular menus based on total sold quantity.
     */
    public function popularMenus()
    {
        $popularItems = Menu::select('menus.*', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('order_items', 'menus.id', '=', 'order_items.menu_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed') // hanya ambil order selesai
            ->groupBy('menus.id')
            ->orderByDesc('total_sold')
            ->take(6) // batasi top 6 menu populer
            ->get();

        return view('customer.popular_menus', compact('popularItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderItem $orderItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrderItem $orderItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrderItem $orderItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderItem $orderItem)
    {
        //
    }
}
