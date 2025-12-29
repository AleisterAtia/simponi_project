<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mr.Wayojiai Buah Premium Padang</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- PENTING untuk AJAX --}}

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine.js --}}
    <script src="//unpkg.com/alpinejs" defer></script>

    {{-- Style --}}
    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f7f7;
        }

        .btn-add {
            background-color: #FF5722;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }

        .btn-add:hover {
            background-color: #F44336;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans pt-[70px]" x-data="wayoujiApp()" x-init="init()">

    <div x-data="{ mobileMenuOpen: false }" class="fixed top-0 left-0 right-0 bg-white shadow-lg z-30 p-3">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-4 sm:px-6 lg:px-8">

            <div class="flex items-center space-x-2">
                <a href="{{ route('landingpage') }}" class="flex items-center space-x-2">
                    <img src="{{ asset('images/LogoToko.jpeg') }}" alt="Wayouji Logo"
                        class="h-10 w-10 border rounded-lg bg-white">
                    <div class="leading-none">
                        <h1 class="text-lg font-bold text-gray-800">Mr.Wayojiai Buah Premium Padang</h1>
                        <p class="text-xs text-gray-500">Elevate Your Taste, Embrace the Premium</p>
                    </div>
                </a>
            </div>

            <div class="hidden md:flex space-x-3 items-center">

                @if (Auth::check())
                    <span class="text-sm text-gray-700">Hi, {{ Auth::user()->name }}</span>

                    {{-- <a href="#"
                        class="text-sm text-gray-600 hover:text-orange-500 transition font-medium">Admin</a> --}}

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center space-x-1.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                @else
                    <button type="button" @click="loginModalOpen = true"
                        class="bg-white border border-gray-300 text-gray-700 hover:border-orange-500 hover:text-orange-500 font-semibold py-2 px-4 rounded-lg flex items-center space-x-1.5 transition duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h1 4m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                            </path>
                        </svg>
                        <span>Login Member</span> </button>

                    <button type="button" @click="showRegisterInfo = true"
    class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center space-x-1.5 transition duration-200">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
        </path>
    </svg>
    <span>Daftar Member</span>
</button>

                    {{-- <a href="#"
                        class="text-sm text-gray-600 hover:text-orange-500 transition font-medium">Admin</a> --}}
                @endif

                <div class="border-l pl-3 ml-1">
                    @include('customer.partials.cart_sidebar')
                </div>
            </div>

            <div class="md:hidden flex items-center space-x-2">
                @include('customer.partials.cart_sidebar')

                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="md:hidden absolute top-full left-0 right-0 bg-white shadow-lg p-4 border-t"
            @click.outside="mobileMenuOpen = false" x-cloak>

            <div class="flex flex-col space-y-3">
                @if (Auth::check())
                    <span class="font-semibold text-gray-800 px-3">Hi, {{ Auth::user()->name }}</span>

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit"
                            class="w-full text-left bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-3 rounded-lg transition flex items-center space-x-1.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                @else
                   <button type="button" 
            @click="mobileMenuOpen = false; loginModalOpen = true"
            class="block w-full text-left bg-white border border-gray-300 text-gray-700 hover:border-orange-500 hover:text-orange-500 font-semibold py-2 px-3 rounded-lg flex items-center space-x-1.5 transition duration-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
            </path>
        </svg>
        <span>Login Member</span>
    </button>

    {{-- TOMBOL DAFTAR MEMBER (MOBILE) --}}
    <button type="button" @click="mobileMenuOpen = false; registerInfoModal = true"
        class="block w-full text-left bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-3 rounded-lg flex items-center space-x-1.5 transition duration-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
            </path>
        </svg>
        <span>Daftar Member</span>
    </button>

                    {{-- <a href="#"
                        class="block w-full text-left px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 hover:text-orange-600 transition font-medium">Admin</a> --}}
                @endif
            </div>
        </div>
    </div>


    @yield('content')
    <footer class="bg-orange-600 text-white mt-10">
        <div class="max-w-7xl mx-auto px-6 py-12">

            {{-- Konten Utama Footer --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">

                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <img src="{{ asset('images/LogoToko.jpeg') }}" alt="Wayouji Logo" class="h-16 rounded-lg">
                        <span class="font-bold text-xl">Mr.Wayojiai Buah Premium Padang</span>
                    </div>
                    <p class="text-sm text-orange-100 leading-relaxed">
                        Nikmati pengalaman premium dengan koleksi minuman berkualitas tinggi, dibuat dengan bahan-bahan
                        pilihan terbaik untuk memanjakan lidah Anda.
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-white mb-4">Kontak</h3>
                    <ul class="space-y-3 text-sm text-orange-100">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-orange-200 mt-0.5"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <span>Jalan Moh.Hatta No.15 Kel,Koto Luar
                                Kec.Pauh Kota Padang</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-orange-200" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.63-1.39-4.86-3.62-6.25-6.25l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                            </svg>
                            <span>0852-1116-2216</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-orange-200" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 10-2.636 6.364M16.5 12V8.25" />
                            </svg>
                            <a href="#" class="hover:text-white transition">@mr.wayojiai</a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-white mb-4">Jam Operasional</h3>
                    <ul class="space-y-3 text-sm text-orange-100">
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-orange-200" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Setiap Hari : 09:00 - 00:00</span>
                        </li>
                        {{-- <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-orange-200" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.807 9.98-3.807 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.75 18.75a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                            </svg>
                            {{-- <span>WIFI: Tersedia</span> Saya ganti 'jlabuah' menjadi 'Tersedia' --}}
                        </li>
                    </ul>
                </div>

            </div>

            {{-- Garis Pemisah & Copyright --}}
            <div class="mt-10 pt-6 border-t border-orange-500">
                <p class="text-center text-sm text-orange-200">
                    © {{ date('Y') }} Mr.Wayojiai Buah Premium Padang. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    @include('customer.partials._checkout_modal')
    @include('customer.partials._confirmation_modal')
    @include('customer.partials._payment_modal')


    {{-- @include('customer.partials._cash_success_modal')
    @include('customer.partials._qris_success_modal') --}}

    @include('customer.partials._topping_modal')
    @include('customer.partials._login_member_modal')

    <div x-show="showRegisterInfo" 
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     x-cloak>
    
    <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 text-center" @click.outside="showRegisterInfo = false">
        <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Pendaftaran Member</h3>
        <p class="text-gray-600 mb-6">Silakan datang langsung ke <strong>Kasir</strong> untuk melakukan pendaftaran member dan aktivasi akun Anda.</p>
        <button @click="showRegisterInfo = false" 
                class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 rounded-xl transition duration-200">
            Mengerti
        </button>
    </div>
</div>

    <script>
        function wayoujiApp() {
            return {
                // ===================================
                // A. SEMUA PROPERTI ANDA
                // ===================================
                loginModalOpen: false,
                showRegisterInfo: false,
                openCart: false,
                checkoutModal: false,
                confirmationModal: false,
                paymentModal: false,
                cashSuccessModal: false,
                qrisSuccessModal: false,
                createdOrder: null,
                selectedPayment: null,
                totalCartPrice: 0,
                errors: {},
                customerInfo: {
                    name: '',
                    phone: '',
                    table_number: '',
                    notes: ''
                },
                submitting: false, // <-- State untuk mencegah klik ganda

                // ⬇️ TAMBAHAN BARU UNTUK REWARD ⬇️
                tab: 'semua',
                search: '', // <-- TAMBAHAN BARU: Untuk tab menu

                // Tambahkan properti untuk menangani data member yang dibutuhkan
                currentPoints: {{ Auth::check() && Auth::user()->customer ? Auth::user()->customer->points : 0 }},
                isMember: {{ Auth::check() && Auth::user()->customer && Auth::user()->customer->is_member ? 'true' : 'false' }},
                // ⬆️ TAMBAHAN BARU UNTUK REWARD ⬆️


                cartItems: [], // Akan berisi item [ { id: 1, name: '...', ... }, ... ]
                cartTotal: 0,
                cartCount: 0,

                toppingModalOpen: false,
                currentMenu: null, // Menyimpan data menu yang diklik
                allToppings: [], // Menyimpan daftar topping dari DB
                selectedToppings: [], // Menyimpan ID topping yang dicentang
                toppingTotal: 0,

                // FUNGSI BARU: Dipanggil saat halaman dimuat
                init() {
                    this.fetchCart();
                    this.loadToppings();
                },

                customerInfo: {
                    // Cek apakah user login? Jika ya, isi nama. Jika tidak, kosong.
                    name: "{{ Auth::check() ? Auth::user()->name : '' }}",

                    // Cek apakah user login & punya data customer? Jika ya, isi no HP.
                    phone: "{{ Auth::check() && Auth::user()->customer ? Auth::user()->customer->phone : '' }}",

                    table_number: '',
                    notes: ''
                },

                async loadToppings() {
                    try {
                        // PERHATIAN: Asumsi route 'toppings.json' sudah ada
                        const response = await fetch('{{ route('toppings.json') }}');
                        if (!response.ok) throw new Error('Failed to fetch toppings');
                        this.allToppings = await response.json();
                    } catch (error) {
                        console.error('Error fetching toppings:', error);
                    }
                },

                // ===================================
                // B. SEMUA FUNGSI ANDA (sekarang menjadi method)
                // ===================================

                formatCurrency(number) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(number);
                },

                showConfirmation() {
                    // 'this' di sini sekarang PASTI mengacu ke objek wayoujiApp
                    const nameValid = this.customerInfo.name && this.customerInfo.name.trim() !== '';
                    const phoneValid = this.customerInfo.phone && this.customerInfo.phone.trim() !== '';
                    if (!nameValid || !phoneValid) {
                        this.errors = {
                            customer_name: !nameValid ? ['Nama pemesan wajib diisi.'] : null,
                            customer_phone: !phoneValid ? ['Nomor telepon wajib diisi.'] : null
                        };
                        this.errors = Object.fromEntries(Object.entries(this.errors).filter(([_, v]) => v != null));
                        return;
                    }
                    this.errors = {};
                    this.checkoutModal = false;
                    this.confirmationModal = true;
                },

                openToppingModal(menu) {
                    this.currentMenu = menu;
                    this.selectedToppings = [];
                    // PERBAIKAN: Ubah string harga menjadi angka
                    this.toppingTotal = parseFloat(menu.price);
                    this.toppingModalOpen = true;
                },

                // FUNGSI BARU: Menghitung total saat topping dicentang
                calculateToppingTotal() {
                    // PERBAIKAN 1: Ubah string harga dasar menjadi angka
                    let newTotal = parseFloat(this.currentMenu.price);

                    this.selectedToppings.forEach(toppingId => {
                        const topping = this.allToppings.find(t => t.id == toppingId);
                        if (topping) {
                            newTotal += parseFloat(topping.price);
                        }
                    });
                    this.toppingTotal = newTotal;
                },

                async fetchCart() {
                    try {
                        const response = await fetch('{{ route('cart.json') }}'); // Route baru
                        const data = await response.json();
                        this.cartItems = data.items;
                        this.cartTotal = data.total;
                        this.cartCount = data.count;
                        this.totalCartPrice = data.total; // Update juga total untuk modal checkout
                    } catch (error) {
                        console.error('Error fetching cart:', error);
                    }
                },

                async addItemToCart() {
                    const menuId = this.currentMenu.id;
                    const toppingIds = this.selectedToppings;

                    try {
                        const response = await fetch('{{ route('cart.add') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                menu_id: menuId,
                                topping_ids: toppingIds // Kirim array topping
                            })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.toppingModalOpen = false; // Tutup modal topping
                            await this.fetchCart(); // Muat ulang data keranjang
                            this.openCart = true; // Buka sidebar keranjang
                        } else {
                            alert('Gagal menambahkan ke keranjang');
                        }
                    } catch (error) {
                        console.error('Error adding to cart:', error);
                    }
                },

                async updateCartQuantity(menuId, quantity) {
                    if (quantity < 1) {
                        return this.removeCartItem(menuId); // Hapus jika qty jadi 0
                    }
                    try {
                        await fetch(`{{ url('/cart/update') }}/${menuId}`, { // Route baru
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                quantity: quantity
                            })
                        });
                        await this.fetchCart(); // Muat ulang data keranjang
                    } catch (error) {
                        console.error('Error updating quantity:', error);
                    }
                },

                // FUNGSI BARU: Hapus item
                async removeCartItem(menuId) {
                    if (!confirm('Yakin ingin menghapus item ini?')) return;
                    try {
                        await fetch(`{{ url('/cart/remove') }}/${menuId}`, { // Route ini mungkin sudah ada
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json'
                            }
                        });
                        await this.fetchCart(); // Muat ulang data keranjang
                    } catch (error) {
                        console.error('Error removing item:', error);
                    }
                },


                // ⬇️ FUNGSI BARU: REDEEM REWARD MEMBER ⬇️
                async redeemReward(rewardId) {
                    if (!confirm('Anda yakin ingin menukarkan reward ini? Poin Anda akan dikurangi secara permanen.')) {
                        return;
                    }

                    try {
                        const response = await fetch(`{{ url('/redeem-reward') }}/${rewardId}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            // 1. Tampilkan Pesan Sukses
                            alert(data.message);

                            // 2. Update Tampilan Poin (Realtime tanpa reload)
                            if (data.new_points !== undefined) {
                                this.currentPoints = data.new_points;
                            }

                            // 3. PROSES DOWNLOAD PDF (BAGIAN BARU)
                            // Mengecek apakah backend mengirimkan link PDF
                            if (data.pdf_url) {
                                // Cara aman mendownload file:
                                // Membuat elemen link sementara, klik otomatis, lalu hapus.
                                const link = document.createElement('a');
                                link.href = data.pdf_url;
                                link.target =
                                    '_blank'; // Opsional: Buka di tab baru jika browser memblokir download langsung
                                link.download = 'Kupon-Reward.pdf'; // Nama file default
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);
                            }

                            // 4. (Opsional) Jangan reload halaman langsung
                            // window.location.reload();
                            // ALASAN MENGHAPUS RELOAD: Jika halaman direload terlalu cepat,
                            // proses download PDF bisa terputus/gagal.
                            // Karena kita pakai AlpineJS, poin sudah terupdate otomatis di langkah no 2.

                        } else {
                            alert('Gagal menukar: ' + (data.message || 'Terjadi kesalahan.'));
                        }
                    } catch (error) {
                        console.error('Error redeeming reward:', error);
                        alert('Terjadi kesalahan jaringan saat menukar reward.');
                    }
                },
                // ⬆️ END FUNGSI REDEEM REWARD ⬆️


                async submitCheckout() {
                    this.errors = {};
                    const formData = new FormData();

                    // 'this.customerInfo' sekarang 100% terdefinisi
                    formData.append('customer_name', this.customerInfo.name || '');
                    formData.append('customer_phone', this.customerInfo.phone || '');
                    formData.append('table_number', this.customerInfo.table_number || '');
                    formData.append('notes', this.customerInfo.notes || '');

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    try {
                        const response = await fetch('{{ route('checkout.store') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                        const data = await response.json();

                        if (!response.ok) {
                            if (response.status === 422) {
                                this.errors = data.errors;
                                this.confirmationModal = false;
                                this.checkoutModal = true;
                            } else {
                                alert(data.message || 'Terjadi kesalahan.');
                            }
                            return;
                        }

                        this.createdOrder = data.order;
                        this.confirmationModal = false;
                        this.selectedPayment = null;
                        this.paymentModal = true;

                    } catch (error) {
                        console.error('Error submitting checkout:', error);
                        alert('Terjadi kesalahan jaringan.');
                    }
                },

                async confirmPayment() {
                    if (!this.selectedPayment) {
                        alert('Silakan pilih metode pembayaran.');
                        return;
                    }

                    // 'this.createdOrder' juga pasti terdefinisi
                    const orderId = this.createdOrder.id;
                    const paymentMethod = this.selectedPayment;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    try {
                        const response = await fetch(`/order/${orderId}/payment`, {
                            method: 'PATCH',
                            body: JSON.stringify({
                                payment_method: paymentMethod
                            }),
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Gagal mengupdate pembayaran.');
                        }

                        this.paymentModal = false;
                        if (paymentMethod === 'cash') {
                            this.cashSuccessModal = true;
                        } else if (paymentMethod === 'qris') {
                            this.qrisSuccessModal = true;
                        }

                        window.location.href = `/track/${orderId}`;

                    } catch (error) {
                        console.error('Error confirming payment:', error);
                        alert('Terjadi kesalahan jaringan saat konfirmasi pembayaran.');
                    }
                }

            } // akhir dari return
        }
    </script>

</body>

</html>
