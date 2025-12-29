<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    // Menampilkan halaman daftar semua menu beserta kategorinya
    public function index()
    {
        $menus = Menu::with('category')->get();
        return view('admin.menu.index', compact('menus'));
    }

    // Menampilkan halaman form untuk membuat menu baru
    public function create()
    {
        $categories = Category::all();
        return view('admin.menu.create', compact('categories'));
    }

    // Menyimpan data menu baru ke database, termasuk proses validasi dan upload gambar
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'status' => 'required|in:tersedia,habis',
            'image' => 'nullable|image|max:51200',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menus', 'public');
        }

        Menu::create($data);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil ditambahkan');
    }

    // Menampilkan halaman form edit untuk menu tertentu berdasarkan ID
    public function edit(Menu $menu)
    {
        $categories = Category::all();
        return view('admin.menu.edit', compact('menu', 'categories'));
    }

    // Memperbarui data menu di database, termasuk menghapus gambar lama jika ada upload gambar baru
    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'status' => 'required|in:tersedia,habis',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $data['image'] = $request->file('image')->store('menus', 'public');
        }

        $menu->update($data);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil diperbarui');
    }

    // Menghapus data menu dari database beserta file gambarnya dari penyimpanan
    public function destroy(Menu $menu)
    {
        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil dihapus');
    }

    // Mengubah status ketersediaan menu (tersedia/habis) dan mengembalikan respon JSON (untuk AJAX)
    public function toggleStatus(Menu $menu)
    {
        $menu->status = (strtolower($menu->status) == 'tersedia') ? 'habis' : 'tersedia';
        $menu->save();

        $isAvailable = strtolower($menu->status) == 'tersedia';
        $badgeColor = $isAvailable ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';

        return response()->json([
            'success' => true,
            'statusText' => $menu->status,
            'badgeColor' => $badgeColor
        ]);
    }
}
