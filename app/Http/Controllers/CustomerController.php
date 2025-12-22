<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer; // Import Model Customer
use Illuminate\Support\Facades\DB; // Digunakan untuk Transaction

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource. (List Customer di halaman admin/kasir)
     * NOTE: Controller ini melayani rute Admin dan Kasir.
     */
    public function index()
    {
        // Ambil data Customer, dan muat relasi user untuk mengakses email
        $customers = Customer::with('user')->get();

        // --- LOGIC PENENTU VIEW ---
        // Jika route yang dipanggil adalah kasir.customers.index, kembalikan view kasir.
        if (request()->routeIs('kasir.customers.index')) {
            return view('kasir.membership', compact('customers'));
        }

        // Default: Kembalikan view admin
        return view('admin.membership', compact('customers'));
    }

    /**
     * Store a newly created resource in storage. (Create User and Customer - AJAX)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email', // Validasi unik di tabel users (login)
            'password'  => 'required|min:6',
            'phone'     => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction(); // Mulai transaksi database

            // 1. Buat entri di tabel USERS (Autentikasi)
            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => 'customer',
            ]);

            // 2. Buat entri di tabel CUSTOMERS (Data Member)
            $memberCode = 'MBR-' . time();

            $customer = Customer::create([
                'user_id'     => $user->id,
                'name'        => $request->name,
                'phone'       => $request->phone,
                'email'       => $request->email,
                'birth_date'  => $request->birth_date,
                'member_code' => $memberCode,
                'is_member'   => true,
                'points'      => 0,
            ]);

            DB::commit();
            return response()->json($customer);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal menyimpan data.'], 500);
        }
    }

    /**
     * Display the specified resource. (Get Customer - AJAX untuk Edit)
     */
    public function show($id)
    {
        $customer = Customer::with('user')->findOrFail($id);

        $data = $customer->toArray();
        $data['email'] = $customer->user->email; // Ambil email dari tabel user
        // 'address' dihapus
        $data['birth_date'] = $customer->birth_date;

        return response()->json($data);
    }

    /**
     * Update the specified resource in storage. (Update User and Customer - AJAX)
     */

    public function login(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'phone' => 'required|numeric',
        ]);

        // 2. Cek apakah No HP ada di tabel customers
        // Sesuai gambar database: kolom 'phone' dan pastikan 'is_member' aktif (opsional)
        $customer = Customer::where('phone', $request->phone)->first();

        if (!$customer) {
            return back()->with('error', 'Nomor HP tidak terdaftar sebagai member.');
        }

        // 3. Proses Login
        // Jika tabel customers punya user_id yang relasi ke tabel users:
        if ($customer->user_id) {
            // Login menggunakan ID User yang terhubung
            Auth::loginUsingId($customer->user_id);

            // Redirect kembali ke halaman utama
            return redirect()->intended('/')->with('success', 'Selamat datang kembali, ' . $customer->name);
        } else {
            // Jika tidak ada relasi user, sesuaikan dengan logika autentikasi aplikasimu
            return back()->with('error', 'Akun member tidak valid.');
        }
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $user = $customer->user;

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'     => 'nullable|string|max:20',
            // 'address' dihapus
            'birth_date'=> 'nullable|date',
        ]);

        DB::beginTransaction();

        // 1. Update tabel USERS (data login)
        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        // 2. Update tabel CUSTOMERS (data member)
        $customer->update([
            'name'      => $request->name,
            'phone'     => $request->phone,
            'email'     => $request->email,
            // 'address' dihapus
            'birth_date'=> $request->birth_date,
        ]);

        DB::commit();
        return response()->json($customer);
    }

    /**
     * Remove the specified resource from storage. (Delete User and Customer - AJAX)
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $user = $customer->user;

        DB::beginTransaction();

        $customer->delete();
        if ($user) {
            $user->delete();
        }

        DB::commit();
        return response()->json(['success' => true]);
    }
}
