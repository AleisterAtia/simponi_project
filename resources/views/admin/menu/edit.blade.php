@extends('admin.layout') {{-- Layout utama --}}

@section('title', 'Edit Menu') {{-- Optional, untuk title halaman --}}

@section('content')
<h2 class="text-2xl font-bold mb-4">Edit Menu: {{ $menu->name }}</h2>

@if ($errors->any())
<div class="bg-red-100 text-red-700 p-2 rounded mb-4">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.menu.update', $menu->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-2">
        <label class="block mb-1">Nama Menu</label>
        <input type="text" name="name" class="w-full border p-2 rounded" value="{{ old('name', $menu->name) }}">
    </div>

    <div class="mb-2">
        <label class="block mb-1">Kategori</label>
        <select name="category_id" class="w-full border p-2 rounded">
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $menu->category_id) == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-2">
        <label class="block mb-1">Deskripsi</label>
        <textarea name="description" class="w-full border p-2 rounded">{{ old('description', $menu->description) }}</textarea>
    </div>

    <div class="mb-2">
        <label class="block mb-1">Harga</label>
        <input type="number" name="price" class="w-full border p-2 rounded" step="0.01" value="{{ old('price', $menu->price) }}">
    </div>

    <div class="mb-2">
        <label class="block mb-1">Stock</label>
        <input type="number" name="stock" class="w-full border p-2 rounded" value="{{ old('stock', $menu->stock) }}">
    </div>

    <div class="mb-2">
        <label class="block mb-1">Status</label>
        <select name="status" class="w-full border p-2 rounded">
            <option value="tersedia" {{ old('status', $menu->status) == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
            <option value="habis" {{ old('status', $menu->status) == 'habis' ? 'selected' : '' }}>Habis</option>
        </select>
    </div>

    <div class="mb-2">
        <label class="block mb-1">Gambar</label>
        @if($menu->image)
            <img src="{{ asset('storage/'.$menu->image) }}" alt="{{ $menu->name }}" class="w-32 h-32 mb-2 object-cover rounded">
        @endif
        <input type="file" name="image" class="w-full border p-2 rounded">
        <small class="text-gray-500">Kosongkan jika tidak ingin mengganti gambar</small>
    </div>

    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Update Menu</button>
    <a href="{{ route('admin.menu.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</a>
</form>
@endsection
