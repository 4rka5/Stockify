@extends('layouts.admin')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')
@section('breadcrumb', 'Home / Kategori / Tambah')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Daftar Kategori
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-6">
        <h3 class="text-xl font-semibold text-gray-800">Form Tambah Kategori</h3>
        <p class="text-sm text-gray-600 mt-1">Lengkapi form di bawah ini untuk menambah kategori baru</p>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf

        <!-- Name -->
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Nama Kategori <span class="text-red-500">*</span>
            </label>
            <input type="text" id="name" name="name" value="{{ old('name') }}"
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
                placeholder="Deskripsi kategori (opsional)">{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
            @enderror
            <p class="mt-1 text-sm text-gray-500">Berikan deskripsi singkat tentang kategori ini</p>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.categories.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                <i class="fas fa-times mr-2"></i>
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-save mr-2"></i>
                Simpan Kategori
            </button>
        </div>
    </form>
</div>

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
                    <li>Nama kategori harus unik dan tidak boleh sama dengan kategori lain</li>
                    <li>Deskripsi bersifat opsional namun disarankan untuk diisi</li>
                    <li>Kategori dapat digunakan untuk mengelompokkan produk</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
