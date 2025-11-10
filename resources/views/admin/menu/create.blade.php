@extends('admin.layout') {{-- Layout utama yang sudah memuat sidebar --}}

@section('title', 'Tambah Menu') {{-- Optional, untuk title halaman --}}

@section('content')
<h2 class="text-2xl font-bold mb-4">Tambah Menu</h2>

@if ($errors->any())
<div class="bg-red-100 text-red-700 p-2 rounded mb-4">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-2">
        <label class="block mb-1">Nama Menu</label>
        <input type="text" name="name" class="w-full border p-2 rounded" value="{{ old('name') }}">
    </div>
    <div class="mb-2">
        <label class="block mb-1">Kategori</label>
        <select name="category_id" class="w-full border p-2 rounded">
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id')==$category->id?'selected':'' }}>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-2">
        <label class="block mb-1">Deskripsi</label>
        <textarea name="description" class="w-full border p-2 rounded">{{ old('description') }}</textarea>
    </div>
    <div class="mb-2">
        <label class="block mb-1">Harga</label>
        <input type="number" name="price" class="w-full border p-2 rounded" step="0.01" value="{{ old('price') }}">
    </div>
    <div class="mb-2">
        <label class="block mb-1">Stock</label>
        <input type="number" name="stock" class="w-full border p-2 rounded" value="{{ old('stock') }}">
    </div>
    <div class="mb-2">
        <label class="block mb-1">Status</label>
        <select name="status" class="w-full border p-2 rounded">
            <option value="tersedia" {{ old('status')=='tersedia'?'selected':'' }}>Tersedia</option>
            <option value="habis" {{ old('status')=='habis'?'selected':'' }}>Habis</option>
        </select>
    </div>
    <div class="mb-4">
        <label class="block mb-1">Gambar</label>
        <input type="file" name="image" class="w-full border p-2 rounded" accept="image/*" onchange="previewImage(event)">
        <div class="mt-2">
            <img id="imagePreview" src="https://placehold.co/150x150/F8F8F8/B0B0B0?text=Preview" alt="Preview" class="w-36 h-36 object-cover rounded border">
        </div>
    </div>
    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Simpan</button>
</form>

{{-- Script untuk preview gambar sebelum upload --}}
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('imagePreview');
            output.src = reader.result;
        };
        if(event.target.files[0]){
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>
@endsection
