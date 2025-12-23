<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Tampilkan daftar semua kategori (Halaman Index).
     */
    public function index()
    {
        // Mengambil kategori terbaru, tambahkan pagination (misalnya 10 per halaman)
        $categories = Category::latest()->paginate(10);
        
        // Pastikan path view ini benar: resources/views/admin/menu/categories.blade.php
        return view('admin.menu.categories', compact('categories'));
    }

    // --- Create (Buat) ---

    /**
     * Tampilkan formulir untuk membuat kategori baru.
     * INI ADALAH FUNGSI YANG KOSONG DAN MENYEBABKAN LAYAR PUTIH.
     * Sekarang fungsi ini akan me-return view 'admin.menu.categories-create'.
     */
    public function create()
    {
        // View yang akan ditampilkan: resources/views/admin/menu/categories-create.blade.php
        return view('admin.menu.categories-create'); 
    }

    /**
     * Simpan kategori yang baru dibuat ke storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        // 2. Buat kategori baru
        Category::create([
            'name' => $request->name,
        ]);

        // 3. Redirect dengan pesan sukses
        return redirect()->route('admin.categories.index')->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    // --- Update (Perbarui) ---

    /**
     * Perbarui resource yang ditentukan di storage.
     * Fungsi ini digunakan oleh Modal Edit di categories.blade.php.
     */
    public function update(Request $request, Category $category)
    {
        // 1. Validasi input (pastikan nama unik, kecuali untuk kategori itu sendiri)
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);
        
        // 2. Update data
        $category->update([
            'name' => $request->name,
        ]);

        // 3. Redirect dengan pesan sukses
        return redirect()->route('admin.categories.index')->with('success', 'Kategori "' . $category->name . '" berhasil diperbarui!');
    }

    // --- Delete (Hapus) ---

    /**
     * Hapus resource yang ditentukan dari storage.
     * Fungsi ini digunakan oleh Modal Delete di categories.blade.php.
     */
    public function destroy(Category $category)
    {
        $categoryName = $category->name;

        // Cek relasi (Opsional, tapi disarankan)
        // Pastikan relasi 'menus' didefinisikan di model Category
        if (method_exists($category, 'menus') && $category->menus()->exists()) {
            return redirect()->route('admin.categories.index')->with('error', 'Gagal menghapus! Kategori "' . $categoryName . '" masih memiliki menu yang terikat.');
        }

        // Hapus kategori
        $category->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('admin.categories.index')->with('success', 'Kategori "' . $categoryName . '" berhasil dihapus!');
    }

    // show() dan edit() tidak diperlukan
    public function show(Category $category)
    {
        // 
    }
    public function edit(Category $category)
    {
        //
    }
}