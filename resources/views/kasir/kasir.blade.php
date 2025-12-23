<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mr.Wayojiai Kasir - Point of Sale</title>

    {{-- PENTING 1: META TAG CSRF (Untuk AJAX/403 Fix) --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- PENTING 2: BOOTSTRAP CSS (Untuk Modal, Tabel, Tombol) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- 3. Load Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script type="module" src="https://unpkg.com/heroicons@2.1.3/24/outline/index.js"></script>
    <style>
        /* Warna background utama yang lebih lembut */
        body {
            background-color: #FFFBF5;
        }
    </style>
</head>

<body class="min-h-screen flex">

    @include('kasir.partials.sidebar')

    <div class="flex-1">

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

    {{-- PENTING 3: JQUERY (Untuk AJAX) --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- PENTING 4: BOOTSTRAP JS (Untuk Modal) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Script kustom dari view akan dimuat di sini --}}
    @yield('scripts')

    <script>
        function manualOrderApp(menusData) {
            return {
                // DATA
                allMenus: menusData,
                tab: 'semua',
                summaryItems: [],
                summaryTotal: 0,

                paymentModal: false,
                selectedPayment: '',
                submitting: false,

                formatCurrency(number) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(number);
                },

                addItem(menu) {
                    const existing = this.summaryItems.find(i => i.id === menu.id);
                    if (existing) {
                        existing.quantity++;
                    } else {
                        this.summaryItems.push({
                            id: menu.id,
                            name: menu.name,
                            price: parseFloat(menu.price),
                            quantity: 1
                        });
                    }
                    this.calculateTotal();
                },

                updateQty(index, change) {
                    this.summaryItems[index].quantity += change;
                    if (this.summaryItems[index].quantity <= 0) {
                        this.summaryItems.splice(index, 1);
                    }
                    this.calculateTotal();
                },

                calculateTotal() {
                    this.summaryTotal = this.summaryItems.reduce((total, item) => total + (item.price * item.quantity), 0);
                    return this.summaryTotal;
                },

                clearSummary() {
                    if (confirm('Hapus semua item?')) {
                        this.summaryItems = [];
                        this.calculateTotal();
                    }
                },

                openPaymentModal() {
                    if (this.summaryItems.length === 0) return;
                    this.calculateTotal();
                    this.selectedPayment = '';
                    this.paymentModal = true;
                },

                confirmFinalPayment() {
                    if (!this.selectedPayment) {
                        alert('Pilih metode pembayaran terlebih dahulu.');
                        return;
                    }
                    this.submitting = true;
                    document.getElementById('manual-order-form').submit();
                }
            }
        }

        function updateClock() {
            const now = new Date();

            // 1. Format Tanggal (Bahasa Indonesia)
            // Contoh: Senin, 13 Oktober 2025
            const dateOptions = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const dateString = now.toLocaleDateString('id-ID', dateOptions);

            // 2. Format Jam (Format 24 jam)
            // Contoh: 20:13:12
            // Catatan: toLocaleTimeString 'id-ID' defaultnya pakai titik (20.13), 
            // kita replace jadi titik dua (:) agar sesuai desain Anda.
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            }).replace(/\./g, ':');

            // 3. Masukkan ke elemen HTML
            document.getElementById('live-date').innerText = dateString;
            document.getElementById('live-time').innerText = timeString + ' WIB';
        }

        // Jalankan fungsi setiap 1 detik (1000ms)
        setInterval(updateClock, 1000);

        // Jalankan sekali saat halaman pertama kali dimuat (agar tidak menunggu 1 detik)
        updateClock();
    </script>
</body>

</html>
