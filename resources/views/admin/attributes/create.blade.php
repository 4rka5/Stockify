@extends('layouts.admin')

@section('title', 'Tambah Template Atribut')
@section('page-title', 'Tambah Template Atribut')
@section('breadcrumb', 'Home / Template Atribut / Tambah')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.attributes.index') }}" class="text-blue-600 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Template
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Form Tambah Template Atribut</h3>
            <p class="text-sm text-gray-600">Buat template atribut yang dapat dipilih saat menambah produk</p>
        </div>

        <form action="{{ route('admin.attributes.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <!-- Nama Atribut -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Template <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                        placeholder="Contoh: Ukuran, Warna, Bahan, dll" required>
                    @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-info-circle"></i> Template ini akan muncul sebagai pilihan saat admin/manajer menambah produk
                    </p>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                        placeholder="Penjelasan tentang template atribut ini (opsional)">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Aktif -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-sm font-medium text-gray-700">
                            Aktifkan template
                        </span>
                    </label>
                    <p class="mt-2 text-xs text-gray-500 ml-8">
                        <i class="fas fa-info-circle"></i> Hanya template aktif yang dapat dipilih saat menambah produk
                    </p>
                </div>

                <!-- Examples -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-blue-800 mb-2">
                        <i class="fas fa-lightbulb mr-1"></i> Contoh Template Atribut:
                    </h4>
                    <ul class="text-xs text-blue-700 space-y-1 ml-4">
                        <li>• <strong>Ukuran</strong> - Nanti admin/manajer bisa mengisi: S, M, L, XL, XXL</li>
                        <li>• <strong>Warna</strong> - Nanti admin/manajer bisa mengisi: Merah, Biru, Hijau, dll</li>
                        <li>• <strong>Bahan</strong> - Nanti admin/manajer bisa mengisi: Katun, Polyester, Sutra, dll</li>
                        <li>• <strong>Varian Rasa</strong> - Nanti admin/manajer bisa mengisi: Coklat, Vanilla, Strawberry, dll</li>
                        <li>• <strong>Kapasitas</strong> - Nanti admin/manajer bisa mengisi: 500ml, 1L, 2L, dll</li>
                    </ul>
                </div>

                <!-- Info Box -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <i class="fas fa-info-circle text-yellow-600 text-xl mr-3"></i>
                        <div>
                            <h4 class="text-sm font-semibold text-yellow-800 mb-1">Cara Penggunaan:</h4>
                            <p class="text-xs text-yellow-700">
                                Setelah template dibuat, admin dan manajer dapat memilih template ini saat menambah produk baru.
                                Mereka hanya perlu memilih template dan mengisi nilai spesifiknya.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.attributes.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Template
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
