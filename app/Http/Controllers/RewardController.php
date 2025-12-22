<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Reward;
use App\Models\Customer;
use Illuminate\Http\Request;
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
        // 1. CEK AUTENTIKASI DAN STATUS MEMBER
        if (!Auth::check()) {
            return response()->json(['message' => 'Anda harus login untuk menukar reward.'], 401);
        }

        $customer = Auth::user()->customer;

        if (!$customer || !$customer->is_member) {
             return response()->json(['message' => 'Akun ini bukan member yang terdaftar.'], 403);
        }

        DB::beginTransaction();
        try {
            // 2. CEK POIN CUKUP
            if ($customer->points < $reward->points_required) {
                return response()->json([
                    'message' => 'Poin Anda tidak mencukupi.',
                    'current_points' => $customer->points
                ], 400);
            }

            // 3. (Opsional) Cek Stok Reward
            if (isset($reward->stock) && $reward->stock <= 0) {
                 return response()->json(['message' => 'Maaf, stok reward ini sudah habis.'], 400);
            }

            // 4. KURANGI POIN DAN UPDATE CUSTOMER
            $customer->points -= $reward->points_required;
            $customer->save();

            // (Opsional) Kurangi Stok Reward
            if (isset($reward->stock)) {
                 $reward->decrement('stock');
            }

            // 5. CATAT TRANSAKSI REDEMPTION (Menggunakan Model Redemption yang benar)
            Redemption::create([
                'customer_id' => $customer->id,
                'reward_id' => $reward->id,
                'points_used' => $reward->points_required,
                'redemption_date' => Carbon::now(), // Diisi sesuai kolom di Model Redemption Anda
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Selamat! Anda berhasil menukarkan {$reward->name}. Poin Anda sekarang: {$customer->points}",
                'new_points' => $customer->points,
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Reward Redemption Error: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal memproses penukaran reward.'], 500);
        }
    }
}
