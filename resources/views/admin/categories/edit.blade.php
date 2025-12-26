@extends('layouts.admin')

@section('title', 'Edit Kategori')
@section('page-title', 'Edit Kategori')
@section('breadcrumb', 'Home / Kategori / Edit')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Daftar Kategori
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-6">
        <h3 class="text-xl font-semibold text-gray-800">Form Edit Kategori</h3>
        <p class="text-sm text-gray-600 mt-1">Perbarui informasi kategori</p>
    </div>

    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Name -->
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Nama Kategori <span class="text-red-500">*</span>
            </label>
            <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                placeholder="Contoh: Elektronik, Makanan, dll"
                required>
            @error('name')
                <p class="mt-1 text-sm text-red-600">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Description -->
        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                Deskripsi
            </label>
            <textarea id="description" name="description" rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                placeholder="Deskripsi kategori (opsional)">{{ old('description', $category->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
            @enderror
            <p class="mt-1 text-sm text-gray-500">Berikan deskripsi singkat tentang kategori ini</p>
        </div>

        <!-- Category Info -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Informasi Kategori</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">Total Produk:</span>
                    <span class="font-semibold text-gray-800 ml-2">{{ $category->products->count() }} produk</span>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.categories.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                <i class="fas fa-times mr-2"></i>
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-save mr-2"></i>
                Update Kategori
            </button>
        </div>
    </form>
</div>

<!-- Warning Card -->
@if($category->products->count() > 0)
<div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">Peringatan</h3>
            <div class="mt-2 text-sm text-yellow-700">
                <p>Kategori ini memiliki {{ $category->products->count() }} produk terkait. Perubahan nama atau deskripsi kategori akan mempengaruhi semua produk yang terkait.</p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Info Card -->
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-400 text-xl"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800">Informasi</h3>
            <div class="mt-2 text-sm text-blue-700">
                <ul class="list-disc list-inside space-y-1">
                    <li>Field yang ditandai dengan <span class="text-red-500">*</span> wajib diisi</li>
                    <li>Pastikan nama kategori tetap relevan dengan produk yang terkait</li>
                    <li>Perubahan akan langsung mempengaruhi tampilan produk di kategori ini</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
