<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Topping;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ToppingController extends Controller
{
    /**
     * Tampilkan daftar semua topping (Halaman Index).
     */
    public function index()
    {
        // Mengambil topping terbaru, tambahkan pagination (misalnya 10 per halaman)
        $toppings = Topping::latest()->paginate(10);
        
        // Melemparkan data ke view toppings.blade.php
        return view('admin.menu.toppings', compact('toppings'));
    }

    /**
     * Fungsi yang sudah ada untuk mengambil data Topping dalam format JSON (digunakan di Cart).
     */
    public function getJson()
    {
        // Ambil hanya kolom yang kita butuhkan
        $toppings = Topping::select('id', 'name', 'price')->get();
        return response()->json($toppings);
    }

    /**
     * Tampilkan formulir untuk membuat topping baru.
     */
    public function create()
    {
        // Karena kita menggunakan CRUD berbasis modal, 
        // Anda mungkin hanya perlu mengarahkan ini ke view 'create' jika tombol '+' bukan modal.
        return view('admin.menu.toppings-create');
    }

    /**
     * Simpan topping yang baru dibuat ke storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'name' => 'required|string|max:255|unique:toppings,name',
            'price' => 'required|numeric|min:0',
        ], [
            'name.unique' => 'Nama topping ini sudah ada. Mohon gunakan nama yang berbeda.',
        ]);

        // 2. Buat topping baru
        Topping::create([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        // 3. Redirect dengan pesan sukses
        return redirect()->route('admin.toppings.index')->with('success', 'Topping baru berhasil ditambahkan!');
    }

    /**
     * Perbarui resource yang ditentukan di storage.
     * Fungsi ini digunakan oleh Modal Edit di toppings.blade.php.
     */
    public function update(Request $request, Topping $topping)
    {
        try {
            // 1. Validasi input
            $request->validate([
                'name' => 'required|string|max:255|unique:toppings,name,' . $topping->id,
                'price' => 'required|numeric|min:0',
            ], [
                'name.unique' => 'Nama topping ini sudah ada. Mohon gunakan nama yang berbeda.',
            ]);

            // 2. Update data
            $topping->update([
                'name' => $request->name,
                'price' => $request->price,
            ]);

            // 3. Redirect dengan pesan sukses
            return redirect()->route('admin.toppings.index')->with('success', 'Topping "' . $topping->name . '" berhasil diperbarui!');
            
        } catch (ValidationException $e) {
            // Jika validasi gagal, kembalikan user dengan error
            return redirect()->route('admin.toppings.index')
                             ->with('error', 'Gagal memperbarui topping. Pastikan nama unik dan harga benar.')
                             ->withErrors($e->errors());
        }
    }

    /**
     * Hapus resource yang ditentukan dari storage.
     * Fungsi ini digunakan oleh Modal Delete di toppings.blade.php.
     */
    public function destroy(Topping $topping)
    {
        $toppingName = $topping->name;

        // Cek relasi ke orderItems (Opsional: Jika ada data yang terkait, beri peringatan)
        // Berdasarkan model Anda, relasi adalah many-to-many melalui order_item_toppings
        // Jika Anda ingin mencegah penghapusan jika topping sedang digunakan, Anda harus 
        // memastikan relasi orderItems() terdefinisi dengan benar untuk pengecekan.
        // Untuk saat ini, kita akan langsung menghapus.

        try {
            $topping->delete();

            // Redirect dengan pesan sukses
            return redirect()->route('admin.toppings.index')->with('success', 'Topping "' . $toppingName . '" berhasil dihapus!');
            
        } catch (\Exception $e) {
            // Tangani error jika terjadi masalah pada database (misal: foreign key constraint)
            return redirect()->route('admin.toppings.index')->with('error', 'Gagal menghapus topping "' . $toppingName . '". Mungkin topping ini masih terhubung dengan data pesanan yang ada.');
        }
    }

    // Fungsi show() dan edit() tidak diperlukan karena menggunakan modal di halaman index
    public function show(Topping $topping)
    {
        //
    }

    public function edit(Topping $topping)
    {
        //
    }
}