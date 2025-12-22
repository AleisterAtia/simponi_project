@extends('admin.layout') {{-- Asumsi ini adalah layout utama Admin Anda --}}

@section('content')
    <div class="p-6 md:p-10" x-data="discountManager()">

        {{-- Header Halaman --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Manajemen Diskon</h1>
            <button @click="openCreateModal()" 
                class="bg-orange-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-orange-600 transition duration-150">
                + Tambah Diskon Baru
            </button>
        </div>

        {{-- Pesan Sukses/Error Laravel --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        {{-- Tampilkan error validasi umum (di luar field) --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">Terdapat kesalahan validasi. Silakan periksa formulir di bawah.</span>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li class="text-xs ml-4 list-disc">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- Tabel Daftar Diskon --}}
        <div class="bg-white shadow-lg rounded-xl overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Diskon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Persen</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($discounts as $discount)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $discount->nama_diskon }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $discount->tipe_diskon === 'TETAP' ? 'bg-blue-100 text-blue-800' : 
                                       ($discount->tipe_diskon === 'ULANG_TAHUN' ? 'bg-yellow-100 text-yellow-800' : 'bg-orange-100 text-orange-800') }}">
                                    {{ $discount->tipe_diskon }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 font-bold">
                                {{ number_format($discount->persentase_nilai, 0) }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                @if ($discount->tanggal_mulai && $discount->tanggal_akhir)
                                    {{ \Carbon\Carbon::parse($discount->tanggal_mulai)->format('d M y') }} - {{ \Carbon\Carbon::parse($discount->tanggal_akhir)->format('d M y') }}
                                @else
                                    <span class="text-gray-400">Permanen</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $discount->status === 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $discount->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <button @click="openEditModal({{ json_encode($discount) }})"
                                    class="text-indigo-600 hover:text-indigo-900">
                                    Edit
                                </button>
                                
                                @if ($discount->tipe_diskon !== 'TETAP' && $discount->tipe_diskon !== 'ULANG_TAHUN')
                                    <form x-data method="POST" :action="'{{ route('admin.discounts.destroy', $discount->id) }}'" class="inline-block"
                                          @submit.prevent="if (confirm('Yakin ingin menghapus diskon ini?')) $root.submit()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data diskon yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $discounts->links() }}
        </div>


        {{-- ================================================= --}}
        {{-- MODAL CREATE/EDIT DISKON (Alpine.js) --}}
        {{-- ================================================= --}}
        <div x-show="isModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-cloak>

            <div @click.outside="isModalOpen = false" x-show="isModalOpen" x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white rounded-xl shadow-2xl w-full max-w-lg p-6">

                <h2 class="text-2xl font-bold mb-4 text-gray-800" x-text="isEditMode ? 'Edit Diskon' : 'Buat Diskon Baru'"></h2>
                
                {{-- 🚨 PERBAIKAN: Menambahkan x-ref="discountForm" --}}
                <form x-ref="discountForm" :action="formAction" method="POST" @submit.prevent="submitForm">
                    @csrf
                    <template x-if="isEditMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="space-y-4">
                        
                        <div>
                            <label for="nama_diskon" class="block text-sm font-medium text-gray-700">Nama Diskon</label>
                            <input type="text" x-model="formData.nama_diskon" id="nama_diskon" name="nama_diskon" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 @error('nama_diskon') border-red-500 @enderror">
                            @error('nama_diskon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="tipe_diskon" class="block text-sm font-medium text-gray-700">Tipe Diskon</label>
                            <select x-model="formData.tipe_diskon" id="tipe_diskon" name="tipe_diskon" required 
                                :disabled="isEditMode && (formData.tipe_diskon === 'TETAP' || formData.tipe_diskon === 'ULANG_TAHUN')"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 disabled:bg-gray-100 @error('tipe_diskon') border-red-500 @enderror">
                                <option value="TETAP">TETAP (Diskon Member Dasar)</option>
                                <option value="ULANG_TAHUN">ULANG TAHUN</option>
                                <option value="SPESIAL">SPESIAL (Diskon Periode)</option>
                            </select>
                            @error('tipe_diskon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            <p x-show="isEditMode && (formData.tipe_diskon === 'TETAP' || formData.tipe_diskon === 'ULANG_TAHUN')" class="text-xs text-red-500 mt-1">Tipe diskon dasar tidak bisa diubah.</p>
                        </div>
                        
                        <div>
                            <label for="persentase_nilai" class="block text-sm font-medium text-gray-700">Persentase Nilai (%)</label>
                            <input type="number" step="0.01" min="0.01" max="100" x-model.number="formData.persentase_nilai" id="persentase_nilai" name="persentase_nilai" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 @error('persentase_nilai') border-red-500 @enderror">
                            @error('persentase_nilai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div x-show="formData.tipe_diskon === 'SPESIAL'" x-transition class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                                <input type="date" x-model="formData.tanggal_mulai" id="tanggal_mulai" name="tanggal_mulai" :required="formData.tipe_diskon === 'SPESIAL'" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 @error('tanggal_mulai') border-red-500 @enderror">
                                @error('tanggal_mulai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                                <input type="date" x-model="formData.tanggal_akhir" id="tanggal_akhir" name="tanggal_akhir" :required="formData.tipe_diskon === 'SPESIAL'" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 @error('tanggal_akhir') border-red-500 @enderror">
                                @error('tanggal_akhir') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select x-model="formData.status" id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 @error('status') border-red-500 @enderror">
                                <option value="Aktif">Aktif</option>
                                <option value="Non-Aktif">Non-Aktif</option>
                            </select>
                            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="isModalOpen = false" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg font-semibold hover:bg-orange-600 transition" x-text="isEditMode ? 'Simpan Perubahan' : 'Tambahkan Diskon'"></button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>

    <script>
        function discountManager() {
            return {
                isModalOpen: false,
                isEditMode: false,
                formAction: '',
                formData: {
                    id: null,
                    nama_diskon: '',
                    tipe_diskon: 'SPESIAL', // Default ke spesial
                    persentase_nilai: 0.01,
                    tanggal_mulai: '',
                    tanggal_akhir: '',
                    status: 'Aktif',
                },

                openCreateModal() {
                    this.isEditMode = false;
                    this.isModalOpen = true;
                    this.formAction = '{{ route('admin.discounts.store') }}';
                    this.resetForm();
                },

                openEditModal(discount) {
                    this.isEditMode = true;
                    this.isModalOpen = true;
                    this.formAction = '{{ url('admin/discounts') }}/' + discount.id;
                    
                    // Isi data dari model yang di-encode
                    this.formData.id = discount.id;
                    this.formData.nama_diskon = discount.nama_diskon;
                    this.formData.tipe_diskon = discount.tipe_diskon;
                    this.formData.persentase_nilai = parseFloat(discount.persentase_nilai);
                    this.formData.status = discount.status;
                    
                    // Tangani tanggal (pastikan format YYYY-MM-DD untuk input type="date")
                    // Menggunakan string kosong jika null, karena input type=date memerlukan string
                    this.formData.tanggal_mulai = discount.tanggal_mulai || '';
                    this.formData.tanggal_akhir = discount.tanggal_akhir || '';
                },

                resetForm() {
                    this.formData.id = null;
                    this.formData.nama_diskon = '';
                    this.formData.tipe_diskon = 'SPESIAL';
                    this.formData.persentase_nilai = 0.01;
                    this.formData.tanggal_mulai = '';
                    this.formData.tanggal_akhir = '';
                    this.formData.status = 'Aktif';
                },
                
                submitForm() {
                    // Logika validasi dasar Alpine
                    if (this.formData.tipe_diskon === 'SPESIAL') {
                        if (!this.formData.tanggal_mulai || !this.formData.tanggal_akhir) {
                            alert('Tanggal Mulai dan Tanggal Akhir wajib diisi untuk Diskon SPESIAL.');
                            return;
                        }
                    }

                    // 🚨 PERBAIKAN KRITIS: Submit form melalui reference
                    this.$refs.discountForm.submit();
                }
            }
        }
    </script>
@endsection