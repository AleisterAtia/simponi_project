@extends('admin.layout') {{-- Asumsikan Anda menggunakan layout admin --}}

@section('title', 'Manajemen Topping Menu')

@section('content')

{{-- Inisialisasi Alpine.js untuk mengelola modal --}}

<div x-data="{
// Tambahkan modal untuk proses Create
createToppingModal: false,
editToppingModal: false,
deleteToppingModal: false,
// currentTopping digunakan untuk Edit dan Delete
currentTopping: { name: '', price: '' },
editFormAction: '',
deleteFormAction: ''
}">

<div class="p-6 bg-white rounded-lg shadow-xl">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Daftar Topping</h2>
        
        {{-- TOMBOL TAMBAH: Menggunakan @click untuk membuka modal, bukan href ke halaman terpisah --}}
        <button @click="createToppingModal = true"
            class="px-4 py-2 text-white bg-orange-600 rounded-lg shadow hover:bg-orange-700 transition duration-150">
            + Tambah Topping
        </button>
    </div>

    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session('error'))
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
            {{ session('error') }}
        </div>
    @endif
    
    {{-- Tampilkan Error Validasi (jika ada error dari POST/PUT request yang kembali ke index) --}}
    @if ($errors->any())
        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
            <p class="font-bold mb-1">Terjadi Kesalahan Validasi:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (empty($toppings) || $toppings->isEmpty())
        <div class="p-6 text-center text-gray-500 bg-gray-50 rounded-lg border-2 border-dashed">
            Belum ada topping yang ditambahkan.
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            No
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama Topping
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Harga
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Aksi</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($toppings as $index => $topping)
                        <tr class="hover:bg-orange-50/50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $topping->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Rp. {{ number_format($topping->price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                
                                {{-- Tombol Edit (Memunculkan Modal Edit) --}}
                                <button @click="
                                    editToppingModal = true;
                                    // Pastikan nilai diubah menjadi string karena input type number di modal edit
                                    currentTopping.name = '{{ $topping->name }}'; 
                                    currentTopping.price = '{{ $topping->price }}'; 
                                    editFormAction = '{{ route('admin.toppings.update', $topping->id) }}';
                                " class="text-indigo-600 hover:text-indigo-900">
                                    Edit
                                </button>
                                
                                {{-- Tombol Hapus (Memunculkan Modal Hapus) --}}
                                <button @click="
                                    deleteToppingModal = true;
                                    currentTopping.name = '{{ $topping->name }}';
                                    deleteFormAction = '{{ route('admin.toppings.destroy', $topping->id) }}';
                                " class="text-red-600 hover:text-red-900">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $toppings->links() }}
        </div>
    @endif
</div>

{{--- MODAL TAMBAH TOPPING BARU (CREATE) ---}}
<div x-show="createToppingModal" 
      x-transition:enter="ease-out duration-300" 
      x-transition:enter-start="opacity-0" 
      x-transition:enter-end="opacity-100" 
      x-transition:leave="ease-in duration-200" 
      x-transition:leave-start="opacity-100" 
      x-transition:leave-end="opacity-0" 
      class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    
    {{-- Overlay --}}
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        {{-- Modal Panel --}}
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
             role="dialog" aria-modal="true" aria-labelledby="create-modal-headline">
            
            {{-- Form untuk Tambah Topping. Mengarah ke route('admin.toppings.store') --}}
            <form action="{{ route('admin.toppings.store') }}" method="POST">
                @csrf

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="create-modal-headline">
                                Tambah Topping Baru
                            </h3>
                            <div class="mt-4 space-y-4">
                                {{-- Input Nama Topping --}}
                                <div>
                                    <label for="new_name" class="block text-sm font-medium text-gray-700">Nama Topping</label>
                                    <input type="text" name="name" id="new_name" 
                                           required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                                </div>
                                
                                {{-- Input Harga --}}
                                <div>
                                    <label for="new_price" class="block text-sm font-medium text-gray-700">Harga (Angka Saja)</label>
                                    <input type="number" name="price" id="new_price" 
                                           required min="0" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan Topping
                    </button>
                    <button @click="createToppingModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{--- MODAL EDIT TOPPING (POP-UP) ---}}
<div x-show="editToppingModal" 
      x-transition:enter="ease-out duration-300" 
      x-transition:enter-start="opacity-0" 
      x-transition:enter-end="opacity-100" 
      x-transition:leave="ease-in duration-200" 
      x-transition:leave-start="opacity-100" 
      x-transition:leave-end="opacity-0" 
      class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    
    {{-- Overlay --}}
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        {{-- Modal Panel --}}
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
             role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            
            <form :action="editFormAction" method="POST">
                @csrf
                @method('PUT') {{-- Metode untuk update --}}

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                                Edit Topping: <span x-text="currentTopping.name" class="font-bold text-orange-600"></span>
                            </h3>
                            <div class="mt-4 space-y-4">
                                {{-- Input Nama Topping --}}
                                <div>
                                    <label for="name_edit" class="block text-sm font-medium text-gray-700">Nama Topping Baru</label>
                                    <input type="text" name="name" id="name_edit" x-model="currentTopping.name" 
                                           required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                                </div>
                                
                                {{-- Input Harga --}}
                                <div>
                                    <label for="price_edit" class="block text-sm font-medium text-gray-700">Harga (Angka Saja)</label>
                                    {{-- Gunakan x-model.number agar nilai yang dikirim selalu berupa angka --}}
                                    <input type="number" name="price" id="price_edit" x-model.number="currentTopping.price" 
                                           required min="0" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan Perubahan
                    </button>
                    <button @click="editToppingModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{--- MODAL HAPUS TOPPING (POP-UP) ---}}
<div x-show="deleteToppingModal" 
      x-transition:enter="ease-out duration-300" 
      x-transition:enter-start="opacity-0" 
      x-transition:enter-end="opacity-100" 
      x-transition:leave="ease-in duration-200" 
      x-transition:leave-start="opacity-100" 
      x-transition:leave-end="opacity-0" 
      class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">

    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
             role="dialog" aria-modal="true" aria-labelledby="delete-modal-headline">
            
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="delete-modal-headline">
                            Hapus Topping
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Apakah Anda yakin ingin menghapus topping **<span x-text="currentTopping.name"></span>**? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <form :action="deleteFormAction" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Ya, Hapus
                    </button>
                </form>
                <button @click="deleteToppingModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>


</div>
@endsection