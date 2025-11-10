<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wayouji Kasir - Point of Sale</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    {{-- Kita akan gunakan ikon dari Heroicons --}}
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
            <div>
                <p class="font-semibold text-gray-700">Senin, 13 Oktober 2025</p>
                <p class="text-sm text-gray-500">20:13:12 WIB</p>
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
    <script>
        function manualOrderApp(menusData) {
            return {
                // DATA
                allMenus: menusData,
                tab: 'semua',
                summaryItems: [],
                summaryTotal: 0, // <-- Tambahkan properti ini

                // State baru untuk modal
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
                    // PERBAIKAN: Simpan ke properti agar 'Total' di modal berfungsi
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
    </script>
</body>

</html>
