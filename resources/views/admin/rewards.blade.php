@extends('admin.layout')

@section('styles')
    {{-- Load Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Google Fonts: Inter --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    {{-- Bootstrap Icons --}}

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#F25C05',
                        'primary-hover': '#d14d02',
                        'primary-light': '#FFF0E6',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        /* Styling khusus untuk modal & scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endsection

@section('content')
    <div class="min-h-screen bg-gray-50 p-6">

        {{-- FLASH MESSAGE (Tailwind Style) --}}
        @if (session('success'))
            <div
                class="mb-6 flex items-center p-4 bg-green-100 border-l-4 border-green-500 rounded shadow-sm animate-fade-in-down">
                <i class="bi bi-check-circle-fill text-green-600 text-xl mr-3"></i>
                <div class="text-green-800 font-medium">{{ session('success') }}</div>
                <button onclick="this.parentElement.remove()" class="ml-auto text-green-600 hover:text-green-800">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif

        {{-- HEADER SECTION --}}
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Manajemen Reward</h1>
                <p class="text-gray-500 mt-1 text-sm">Kelola daftar hadiah dan penukaran poin member.</p>
            </div>
            <button onclick="openModal('addRewardModal')"
                class="mt-4 sm:mt-0 bg-orange-500 hover:bg-primary-hover text-white font-semibold py-2.5 px-6 rounded-xl shadow-lg shadow-orange-200 transition-all duration-300 transform hover:-translate-y-1 flex items-center group">
                <i class="bi bi-plus-lg mr-2 group-hover:rotate-90 transition-transform"></i> Tambah Reward
            </button>
        </div>

        {{-- CARD TABLE --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-primary-light text-orange-500 border-b border-orange-100">
                            <th class="p-5 font-bold text-xs uppercase tracking-wider w-16 text-center">No</th>
                            <th class="p-5 font-bold text-xs uppercase tracking-wider">Nama Reward</th>
                            {{-- <th class="p-5 font-bold text-xs uppercase tracking-wider">Terkait Menu</th> --}}
                            {{-- Tambahan kolom info --}}
                            <th class="p-5 font-bold text-xs uppercase tracking-wider text-center">Poin</th>
                            <th class="p-5 font-bold text-xs uppercase tracking-wider text-center">Stok</th>
                            <th class="p-5 font-bold text-xs uppercase tracking-wider text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($rewards as $index => $reward)
                            <tr class="hover:bg-gray-50 transition duration-150 group">
                                <td class="p-5 text-center text-gray-500 font-medium">
                                    {{ $rewards->firstItem() + $index }}
                                </td>
                                <td class="p-5">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-orange-50 text-orange-500 flex items-center justify-center text-xl mr-3 group-hover:scale-110 transition-transform duration-300">
                                            <i class="bi bi-gift-fill"></i>
                                        </div>
                                        <span class="font-semibold text-gray-700">{{ $reward->name }}</span>
                                    </div>
                                </td>
                                {{-- <td class="p-5 text-sm text-gray-600">
                                    @if ($reward->menu)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                            <i class="bi bi-cup-hot-fill mr-1"></i> {{ $reward->menu->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic text-xs">- Tidak ada -</span>
                                    @endif
                                </td> --}}
                                <td class="p-5 text-center">
                                    <span
                                        class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full border border-yellow-200">
                                        {{ number_format($reward->points_required) }} Poin
                                    </span>
                                </td>
                                <td class="p-5 text-center">
                                    @if ($reward->stock > 0)
                                        <span class="text-gray-700 font-medium">{{ $reward->stock }}</span>
                                    @else
                                        <span
                                            class="text-red-500 text-xs font-bold bg-red-50 px-2 py-1 rounded">Habis</span>
                                    @endif
                                </td>
                                <td class="p-5 text-center">
                                    <div class="flex justify-center space-x-2">
                                        {{-- Edit Button --}}
                                        <button
                                            onclick="openEditModal({{ $reward->id }}, '{{ $reward->name }}', '{{ $reward->menu_id }}', {{ $reward->points_required }}, {{ $reward->stock }})"
                                            class="w-8 h-8 rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-500 hover:text-white transition flex items-center justify-center"
                                            title="Edit">
                                            <i class="bi bi-pencil-fill text-xs"></i>
                                        </button>

                                        {{-- Delete Form --}}
                                        <form action="{{ route('admin.rewards.destroy', $reward->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus reward ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-500 hover:text-white transition flex items-center justify-center"
                                                title="Hapus">
                                                <i class="bi bi-trash-fill text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-10 text-center text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                            <i class="bi bi-inbox text-3xl opacity-50"></i>
                                        </div>
                                        <p class="text-sm font-medium">Belum ada data reward.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($rewards->hasPages())
                <div class="p-4 border-t border-gray-100 bg-gray-50">
                    {{ $rewards->links() }}
                    {{-- Note: Pastikan Laravel menggunakan Tailwind pagination view --}}
                </div>
            @endif
        </div>
    </div>

    {{-- ===================== MODAL TAMBAH REWARD ===================== --}}
    <div id="addRewardModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity backdrop-blur-sm"
            onclick="closeModal('addRewardModal')"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border-t-4 border-orange-500">

                    {{-- Header --}}
                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <i class="bi bi-gift-fill text-orange-500 mr-2"></i> Tambah Reward Baru
                        </h3>
                        <button type="button" onclick="closeModal('addRewardModal')"
                            class="text-gray-400 hover:text-gray-500 transition">
                            <i class="bi bi-x-lg text-lg"></i>
                        </button>
                    </div>

                    {{-- Form Body --}}
                    <form action="{{ route('admin.rewards.store') }}" method="POST" class="px-6 py-6 space-y-5">
                        @csrf

                        {{-- Nama Reward --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Reward <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" required placeholder="Contoh: Tumbler Eksklusif"
                                class="w-full rounded-lg border-gray-300 border px-3 py-2.5 text-sm focus:border-primary focus:ring-primary focus:ring-1 outline-none transition shadow-sm">
                        </div>

                        {{-- Menu Dropdown --}}
                        {{-- <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tautkan Menu (Opsional)</label>
                            <select name="menu_id"
                                class="w-full rounded-lg border-gray-300 border px-3 py-2.5 text-sm focus:border-primary focus:ring-primary focus:ring-1 outline-none transition shadow-sm bg-white">
                                <option value="">-- Tidak Ada (Barang Fisik) --</option>
                                @foreach ($menus as $menu)
                                    <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Pilih jika reward berupa menu gratis.</p>
                        </div> --}}

                        {{-- Grid Poin & Stok --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Poin Dibutuhkan <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-star-fill text-yellow-400 text-xs"></i>
                                    </div>
                                    <input type="number" name="points_required" required min="1" placeholder="0"
                                        class="w-full rounded-lg border-gray-300 border pl-8 pr-3 py-2.5 text-sm focus:border-primary focus:ring-primary focus:ring-1 outline-none transition shadow-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stok Awal <span
                                        class="text-red-500">*</span></label>
                                <input type="number" name="stock" required min="0" placeholder="0"
                                    class="w-full rounded-lg border-gray-300 border px-3 py-2.5 text-sm focus:border-primary focus:ring-primary focus:ring-1 outline-none transition shadow-sm">
                            </div>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="pt-4 flex justify-end gap-3">
                            <button type="button" onclick="closeModal('addRewardModal')"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 text-sm font-medium hover:bg-gray-50 transition">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-primary-hover shadow-md transition flex items-center">
                                <i class="bi bi-save mr-2"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== MODAL EDIT REWARD (Reusable) ===================== --}}
    <div id="editRewardModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity backdrop-blur-sm"
            onclick="closeModal('editRewardModal')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border-t-4 border-yellow-400">

                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <i class="bi bi-pencil-square text-yellow-500 mr-2"></i> Edit Reward
                        </h3>
                        <button type="button" onclick="closeModal('editRewardModal')"
                            class="text-gray-400 hover:text-gray-500 transition"><i
                                class="bi bi-x-lg text-lg"></i></button>
                    </div>

                    {{-- Form Edit akan diinject URL action-nya via JS --}}
                    <form id="editForm" method="POST" class="px-6 py-6 space-y-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Reward</label>
                            <input type="text" name="name" id="edit_name" required
                                class="w-full rounded-lg border-gray-300 border px-3 py-2.5 text-sm focus:border-yellow-500 focus:ring-yellow-500 focus:ring-1 outline-none transition shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tautkan Menu</label>
                            <select name="menu_id" id="edit_menu_id"
                                class="w-full rounded-lg border-gray-300 border px-3 py-2.5 text-sm focus:border-yellow-500 focus:ring-yellow-500 focus:ring-1 outline-none transition shadow-sm bg-white">
                                <option value="">-- Tidak Ada --</option>
                                @foreach ($menus as $menu)
                                    <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Poin</label>
                                <input type="number" name="points_required" id="edit_points" required min="1"
                                    class="w-full rounded-lg border-gray-300 border px-3 py-2.5 text-sm focus:border-yellow-500 focus:ring-yellow-500 focus:ring-1 outline-none transition shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                                <input type="number" name="stock" id="edit_stock" required min="0"
                                    class="w-full rounded-lg border-gray-300 border px-3 py-2.5 text-sm focus:border-yellow-500 focus:ring-yellow-500 focus:ring-1 outline-none transition shadow-sm">
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end gap-3">
                            <button type="button" onclick="closeModal('editRewardModal')"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 text-sm font-medium hover:bg-gray-50 transition">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-yellow-500 text-white rounded-lg text-sm font-medium hover:bg-yellow-600 shadow-md transition flex items-center">
                                <i class="bi bi-save mr-2"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Fungsi Buka Modal (Manipulasi class 'hidden' Tailwind)
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        // Fungsi Tutup Modal
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Fungsi isi form Edit secara dinamis (Tanpa reload/AJAX berat)
        function openEditModal(id, name, menuId, points, stock) {
            // Set URL Action Form
            document.getElementById('editForm').action = `/admin/rewards/${id}`;

            // Isi Value Input
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_points').value = points;
            document.getElementById('edit_stock').value = stock;

            // Set Selected Menu (Handle null/empty string)
            let menuSelect = document.getElementById('edit_menu_id');
            menuSelect.value = menuId || ""; // Jika null, set ke ""

            openModal('editRewardModal');
        }
    </script>
@endsection
