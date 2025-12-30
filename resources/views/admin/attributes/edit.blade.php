@extends('layouts.admin')

@section('title', 'Edit Atribut Produk')
@section('page-title', 'Edit Atribut Produk')
@section('breadcrumb', 'Home / Atribut Produk / Edit')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.attributes.index') }}" class="text-blue-600 hover:text-blue-700">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Atribut
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <form action="{{ route('admin.attributes.update', $attribute->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Produk <span class="text-red-600">*</span>
            </label>
            <select name="product_id"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('product_id') border-red-500 @enderror">
                <option value="">Pilih Produk</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id', $attribute->product_id) == $product->id ? 'selected' : '' }}>
                        {{ $product->name }} ({{ $product->sku }})
                    </option>
                @endforeach
            </select>
            @error('product_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Nama Atribut <span class="text-red-600">*</span>
            </label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $attribute->name) }}"
                   placeholder="Contoh: Ukuran, Warna, Berat"
                   required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Nilai <span class="text-red-600">*</span>
            </label>
            <input type="text"
                   name="value"
                   value="{{ old('value', $attribute->value) }}"
                   placeholder="Contoh: XL, Merah, 500g"
                   required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('value') border-red-500 @enderror">
            @error('value')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-save mr-2"></i>
                Update
            </button>
            <a href="{{ route('admin.attributes.index') }}"
               class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                <i class="fas fa-times mr-2"></i>
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
