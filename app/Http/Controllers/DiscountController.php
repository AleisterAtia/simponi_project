<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DiscountController extends Controller
{
    // ===================================================================
    // LOGIKA UTAMA: PENGHITUNGAN DISKON (Dipanggil oleh OrderController)
    // ===================================================================
    /**
     * Menghitung total persentase diskon yang diperoleh seorang member.
     * @param \App\Models\Customer $customer
     * @param string $transactionDate
     * @return float Total persentase diskon kumulatif.
     */
    public function calculateDiscountPercentage(Customer $customer, $transactionDate = null)
    {
        $date = $transactionDate ? Carbon::parse($transactionDate) : Carbon::now();
        $totalDiscount = 0.00;

        if (!$customer->is_member) {
            return 0.00;
        }

        // --- 1. DISKON TETAP (10%) ---
        // Asumsi: Model Discount memiliki scope active()
        $fixedDiscount = Discount::active()->where('tipe_diskon', 'TETAP')->value('persentase_nilai') ?? 0.00;
        $totalDiscount += $fixedDiscount;

        // --- 2. DISKON ULANG TAHUN (25% TAMBAHAN) ---
        if ($customer->birth_date && $date->month === $customer->birth_date->month && $date->day === $customer->birth_date->day) {
            $birthdayDiscount = Discount::active()->where('tipe_diskon', 'ULANG_TAHUN')->value('persentase_nilai') ?? 0.00;
            $totalDiscount += $birthdayDiscount;
        }

        // --- 3. DISKON HARI SPESIAL (TAMBAHAN) ---
        $specialDiscount = Discount::active()
            ->where('tipe_diskon', 'SPESIAL')
            ->whereDate('tanggal_mulai', '<=', $date)
            ->whereDate('tanggal_akhir', '>=', $date)
            ->sum('persentase_nilai');

        $totalDiscount += $specialDiscount;

        return min(100.00, $totalDiscount);
    }

    // ===================================================================
    // ⬇️ FUNGSI CRUD ADMIN (FIX UNTUK ERROR RouteNotFound) ⬇️
    // ===================================================================

    /**
     * Menampilkan daftar semua diskon (untuk tabel Admin).
     */
    public function index()
    {
        // Ambil semua diskon dan urutkan berdasarkan tipe
        $discounts = Discount::orderBy('tipe_diskon')->paginate(10);

        // Mengarahkan ke view yang Anda tentukan
        return view('admin.diskon', compact('discounts'));
    }

    /**
     * Menyimpan data diskon baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_diskon' => 'required|string|max:100',
            'tipe_diskon' => 'required|in:TETAP,ULANG_TAHUN,SPESIAL',
            'persentase_nilai' => 'required|numeric|min:0.01|max:100',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:Aktif,Non-Aktif',
        ]);

        // Atur tanggal ke NULL jika tipe diskon TETAP atau ULANG_TAHUN
        if ($validated['tipe_diskon'] !== 'SPESIAL') {
            $validated['tanggal_mulai'] = null;
            $validated['tanggal_akhir'] = null;
        }

        Discount::create($validated);

        return redirect()->route('admin.discounts.index')
                         ->with('success', 'Diskon berhasil ditambahkan.');
    }

    /**
     * Memperbarui diskon yang sudah ada.
     */
    public function update(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'nama_diskon' => 'required|string|max:100',
            'tipe_diskon' => 'required|in:TETAP,ULANG_TAHUN,SPESIAL',
            'persentase_nilai' => 'required|numeric|min:0.01|max:100',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:Aktif,Non-Aktif',
        ]);

        // Atur tanggal ke NULL jika tipe diskon TETAP atau ULANG_TAHUN
        if ($validated['tipe_diskon'] !== 'SPESIAL') {
            $validated['tanggal_mulai'] = null;
            $validated['tanggal_akhir'] = null;
        }

        $discount->update($validated);

        return redirect()->route('admin.discounts.index')
                         ->with('success', 'Diskon berhasil diperbarui.');
    }

    /**
     * Menghapus diskon.
     */
    public function destroy(Discount $discount)
    {
        try {
            // Kita cegah penghapusan diskon TETAP atau ULANG_TAHUN (Safety Check)
            // if ($discount->tipe_diskon === 'TETAP' || $discount->tipe_diskon === 'ULANG_TAHUN') {
            //      return back()->with('error', 'Diskon Tetap/Ulang Tahun tidak dapat dihapus, hanya dapat diubah status atau persentasenya.');
            // }

            $discount->delete();
            return redirect()->route('admin.discounts.index')
                             ->with('success', 'Diskon berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting discount: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus diskon.');
        }
    }
}
