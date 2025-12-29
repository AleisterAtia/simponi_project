<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Reward;
use App\Models\Customer;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Untuk mengisi redemption_date
use App\Models\Redemption; // ⬅️ FIX: Model Redemption yang benar

class RewardController extends Controller
{
    // Fungsi CRUD Admin
    public function index()
    {
        $rewards = Reward::latest()->paginate(10);
        $menus = Menu::all();
        return view('admin.rewards', compact('rewards', 'menus'));
    }

public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'name'            => 'required|string|max:255',
            'points_required' => 'required|integer|min:1',
            'stock'           => 'required|integer|min:0',
            // Ubah jadi nullable jika reward tidak harus berupa menu makanan
            'menu_id'         => 'nullable|exists:menus,id',
        ]);

        Reward::create($request->all());

        return redirect()->route('admin.rewards.index')
                         ->with('success', 'Reward berhasil ditambahkan!');
    }

    public function update(Request $request, Reward $reward)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'points_required' => 'required|integer|min:1',
            'stock'           => 'required|integer|min:0',
            'menu_id'         => 'nullable|exists:menus,id',
        ]);

        $reward->update($request->all());

        return redirect()->route('admin.rewards.index')
                         ->with('success', 'Reward berhasil diperbarui!');
    }

    public function destroy(Reward $reward)
    {
        $reward->delete();

        return redirect()->route('admin.rewards.index')
                         ->with('success', 'Reward berhasil dihapus!');
    }

    /**
     * Memproses penukaran item reward oleh customer yang login (melalui AJAX).
     */
    public function redeemReward(Request $request, Reward $reward)
    {
        // 1. Validasi Login & Member
        if (!Auth::check()) return response()->json(['message' => 'Login diperlukan.'], 401);

        $customer = Auth::user()->customer;
        if (!$customer || !$customer->is_member) {
             return response()->json(['message' => 'Khusus member.'], 403);
        }

        DB::beginTransaction();
        try {
            // 2. Cek Poin & Stok
            if ($customer->points < $reward->points_required) {
                return response()->json(['message' => 'Poin kurang.'], 400);
            }
            if ($reward->stock <= 0) {
                 return response()->json(['message' => 'Stok habis.'], 400);
            }

            // 3. Kurangi Poin & Stok
            $customer->decrement('points', $reward->points_required);
            $reward->decrement('stock');

            // 4. Simpan Transaksi (Pakai kolom standar saja)
            $redemption = Redemption::create([
                'customer_id'     => $customer->id,
                'reward_id'       => $reward->id,
                'points_used'     => $reward->points_required,
                'redemption_date' => Carbon::now(), // Pastikan kolom ini ada di DB atau pakai created_at
            ]);

            DB::commit();

            // 5. Return URL PDF
            return response()->json([
                'success' => true,
                'message' => 'Berhasil! Kupon sedang diunduh.',
                'new_points' => $customer->points,
                'pdf_url' => route('rewards.download_coupon', $redemption->id)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Gagal memproses: ' . $e->getMessage()], 500);
        }
    }

    public function downloadCoupon(Redemption $redemption)
{
    // 1. Validasi Kepemilikan
    if (Auth::user()->customer->id !== $redemption->customer_id) {
        abort(403);
    }

    // 2. Load data Reward & Customer agar namanya bisa diambil
    $redemption->load(['reward', 'customer']);

    // 3. Generate Kode Unik
    $couponCode = 'RDM-' . str_pad($redemption->id, 5, '0', STR_PAD_LEFT);

    // 4. PERBAIKAN ZONA WAKTU (TIMEZONE FIX)
    // Ambil waktu dibuat, lalu ubah ke Asia/Jakarta (WIB)
    $createdAt = Carbon::parse($redemption->created_at)->setTimezone('Asia/Jakarta');

    // Hitung kadaluarsa (24 jam dari waktu WIB tersebut)
    $expiresAt = $createdAt->copy()->addHours(24);

    // 5. Generate PDF
    // Kita kirim variabel $createdAt juga agar tanggal transaksi di bawah ikut berubah
    $pdf = Pdf::loadView('pdf.coupon', compact('redemption', 'couponCode', 'expiresAt', 'createdAt'));
    $pdf->setPaper('A5', 'portrait');

    // Nama file saat didownload
    return $pdf->download('Kupon-'.$couponCode.'.pdf');
}
}
