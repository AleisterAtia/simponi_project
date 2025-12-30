<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mr.Wayojiai Kasir - Point of Sale</title>

    {{-- PENTING 1: META TAG CSRF --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- PENTING 2: BOOTSTRAP CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- 3. Load Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    {{-- TAILWIND & ALPINE --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script type="module" src="https://unpkg.com/heroicons@2.1.3/24/outline/index.js"></script>

    <style>
        /* Warna background utama yang lebih lembut */
        body {
            background-color: #FFFBF5;
        }

        /* Mencegah kedip pada elemen Alpine saat loading */
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="min-h-screen flex">

    @include('kasir.partials.sidebar')

    <div class="flex-1 w-full md:ml-64 transition-all">

        <header class="flex justify-between items-center p-4 border-b-2 border-orange-100">
            <div class="text-right">
                <p id="live-date" class="font-semibold text-gray-700">Memuat tanggal...</p>
                <p id="live-time" class="text-sm text-gray-500">Memuat jam...</p>
            </div>
            <button
                class="bg-orange-500 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-orange-600 transition">
                Online
            </button>
        </header>

        <main class="p-6">
            @yield('content')
        </main>

    </div>

    {{-- PENTING 3: JQUERY --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- PENTING 4: BOOTSTRAP JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Script kustom dari view akan dimuat di sini --}}
    @yield('scripts')

    {{-- SCRIPT LOGIKA UTAMA (SUDAH DIPERBAIKI) --}}
    {{-- GANTI BAGIAN SCRIPT PALING BAWAH DI FILE LAYOUT ANDA DENGAN INI --}}
    <script>
        function manualOrderApp(menusData, toppingsData) {
            return {
                // DATA UTAMA
                tab: 'semua',
                menus: menusData,
                allToppings: toppingsData,
                summaryItems: [],

                // STATE MODAL
                showModal: false,
                tempItem: null,
                tempSelectedToppings: [], // SEKARANG HANYA MENYIMPAN ID (Contoh: [1, 3])

                // HITUNG TOTAL BELANJA UTAMA
                get summaryTotal() {
                    return this.summaryItems.reduce((total, item) => {
                        return total + (item.totalPricePerItem * item.quantity);
                    }, 0);
                },

                formatCurrency(value) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(value);
                },

                // --- LOGIKA MODAL ---

                openModal(menu) {
                    this.tempItem = menu;
                    this.tempSelectedToppings = []; // Reset array ID
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
                    this.tempItem = null;
                },

                // 3. PERBAIKAN: Hitung harga berdasarkan ID yang dipilih
                calculateTempTotal() {
                    if (!this.tempItem) return 0;
                    let basePrice = Number(this.tempItem.price);

                    // Loop ID topping yang dipilih, cari objek aslinya, lalu ambil harganya
                    let toppingTotal = this.tempSelectedToppings.reduce((sum, toppingId) => {
                        // Cari data topping lengkap berdasarkan ID
                        let topping = this.allToppings.find(t => t.id == toppingId);
                        return sum + (topping ? Number(topping.price) : 0);
                    }, 0);

                    return basePrice + toppingTotal;
                },

                // 4. PERBAIKAN: Simpan item dengan mengambil objek topping lengkap
                confirmAddItem() {
                    if (!this.tempItem) return;

                    let finalPrice = this.calculateTempTotal();

                    // Kembalikan ID topping menjadi Objek Topping Lengkap untuk disimpan di keranjang
                    // Agar di struk/view nanti namanya muncul
                    let selectedToppingObjects = this.tempSelectedToppings.map(id => {
                        return this.allToppings.find(t => t.id == id);
                    }).filter(t => t); // Filter jika ada yang undefined

                    this.summaryItems.push({
                        id: this.tempItem.id,
                        name: this.tempItem.name,
                        basePrice: this.tempItem.price,
                        selectedToppings: JSON.parse(JSON.stringify(selectedToppingObjects)),
                        totalPricePerItem: finalPrice,
                        quantity: 1
                    });

                    this.closeModal();
                },

                // --- LOGIKA KERANJANG ---
                updateQty(index, change) {
                    if (this.summaryItems[index].quantity + change > 0) {
                        this.summaryItems[index].quantity += change;
                    } else {
                        if (confirm('Hapus item ini?')) {
                            this.summaryItems.splice(index, 1);
                        }
                    }
                },

                clearSummary() {
                    if (confirm('Hapus semua item?')) {
                        this.summaryItems = [];
                    }
                }
            }
        }

        // JAM DIGITAL
        function updateClock() {
            const now = new Date();
            const dateOptions = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            }).replace(/\./g, ':');

            const dateEl = document.getElementById('live-date');
            const timeEl = document.getElementById('live-time');

            if (dateEl) dateEl.innerText = now.toLocaleDateString('id-ID', dateOptions);
            if (timeEl) timeEl.innerText = timeString + ' WIB';
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>

</html>
