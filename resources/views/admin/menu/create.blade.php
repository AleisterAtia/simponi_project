@extends('admin.layout')

@section('title', 'Tambah Menu')

@section('content')
    <div class="max-w-4xl ml-64 mx-auto py-6">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Tambah Menu Baru</h2>
            <a href="{{ route('admin.menu.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Kembali
            </a>
        </div>

        {{-- Alert Error --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada inputan Anda:</h3>
                        <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form Card --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 sm:p-8">
                <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Kiri: Informasi Dasar --}}
                        <div class="space-y-6">

                            {{-- Nama Menu --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Menu <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="name"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-150 ease-in-out shadow-sm"
                                    placeholder="Contoh: Jus Mangga Spesial" value="{{ old('name') }}" required>
                            </div>

                            {{-- Kategori --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select name="category_id"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg appearance-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-150 ease-in-out shadow-sm bg-white"
                                        required>
                                        <option value="" disabled selected>-- Pilih Kategori --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Harga & Stok (Grid Kecil) --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Harga (Rp) <span
                                            class="text-red-500">*</span></label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input type="number" name="price"
                                            class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition shadow-sm"
                                            placeholder="0" step="0.01" min="0" value="{{ old('price') }}"
                                            required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Stok Awal <span
                                            class="text-red-500">*</span></label>
                                    <input type="number" name="stock"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition shadow-sm"
                                        placeholder="0" min="0" value="{{ old('stock') }}" required>
                                </div>
                            </div>

                            {{-- Status --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Status Ketersediaan</label>
                                <select name="status"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition shadow-sm bg-white">
                                    <option value="tersedia" {{ old('status') == 'tersedia' ? 'selected' : '' }}>✅ Tersedia
                                    </option>
                                    <option value="habis" {{ old('status') == 'habis' ? 'selected' : '' }}>❌ Habis</option>
                                </select>
                            </div>

                        </div>

                        {{-- Kanan: Deskripsi & Gambar --}}
                        <div class="space-y-6">

                            {{-- Deskripsi --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Produk</label>
                                <textarea name="description" rows="4"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition shadow-sm resize-none"
                                    placeholder="Jelaskan detail menu, bahan, atau rasa...">{{ old('description') }}</textarea>
                            </div>

                            {{-- Upload Gambar dengan Preview Modern --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Menu</label>

                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:bg-gray-50 transition cursor-pointer relative group"
                                    onclick="document.getElementById('file-upload').click()">
                                    <div class="space-y-1 text-center">

                                        {{-- Preview Container --}}
                                        <div
                                            class="mx-auto h-48 w-full relative flex items-center justify-center overflow-hidden rounded-md bg-gray-100">
                                            <img id="imagePreview" src="#" alt="Preview"
                                                class="hidden h-full w-full object-cover">

                                            {{-- Placeholder Icon (Hidden when image loaded) --}}
                                            <div id="uploadPlaceholder" class="flex flex-col items-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                                    fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                    <path
                                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex text-sm text-gray-600 mt-2">
                                                    <span
                                                        class="relative cursor-pointer bg-white rounded-md font-medium text-orange-600 hover:text-orange-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-orange-500">
                                                        <span>Upload file</span>
                                                    </span>
                                                    <p class="pl-1">atau drag & drop</p>
                                                </div>
                                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                            </div>
                                        </div>

                                        <input id="file-upload" name="image" type="file" class="sr-only"
                                            accept="image/*" onchange="previewImage(event)">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end space-x-3">
                        <a href="{{ route('admin.menu.index') }}"
                            class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition shadow-sm">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition shadow-lg shadow-orange-200 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            Simpan Menu
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- Script Preview Image Modern --}}
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            const file = event.target.files[0];
            const preview = document.getElementById('imagePreview');
            const placeholder = document.getElementById('uploadPlaceholder');

            reader.onload = function() {
                preview.src = reader.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection
