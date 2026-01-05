@extends('layouts.admin')

@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk')
@section('breadcrumb', 'Home / Produk / Tambah')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.products.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Daftar Produk
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-6">
        <h3 class="text-xl font-semibold text-gray-800">Form Tambah Produk</h3>
        <p class="text-sm text-gray-600 mt-1">Lengkapi form di bawah ini untuk menambah produk baru</p>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Produk <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                        placeholder="Contoh: Laptop Asus ROG"
                        required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- SKU -->
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">
                        SKU (Stock Keeping Unit)
                    </label>
                    <input type="text" id="sku" name="sku" value="{{ old('sku') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('sku') border-red-500 @enderror"
                        placeholder="Otomatis jika dikosongkan">
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Akan dibuat otomatis jika dikosongkan</p>
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select id="category_id" name="category_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category_id') border-red-500 @enderror"
                        required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Supplier -->
                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Supplier <span class="text-red-500">*</span>
                    </label>
                    <select id="supplier_id" name="supplier_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('supplier_id') border-red-500 @enderror"
                        required>
                        <option value="">Pilih Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Purchase Price -->
                <div>
                    <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-2">
                        Harga Beli <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                        <input type="number" id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}"
                            class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('purchase_price') border-red-500 @enderror"
                            placeholder="0"
                            min="0"
                            step="0.01"
                            required>
                    </div>
                    @error('purchase_price')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Selling Price -->
                <div>
                    <label for="selling_price" class="block text-sm font-medium text-gray-700 mb-2">
                        Harga Jual <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                        <input type="number" id="selling_price" name="selling_price" value="{{ old('selling_price') }}"
                            class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('selling_price') border-red-500 @enderror"
                            placeholder="0"
                            min="0"
                            step="0.01"
                            required>
                    </div>
                    @error('selling_price')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Minimum Stock -->
                <div>
                    <label for="minimum_stock" class="block text-sm font-medium text-gray-700 mb-2">
                        Stok Minimum <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('minimum_stock') border-red-500 @enderror"
                        placeholder="10"
                        min="0"
                        required>
                    @error('minimum_stock')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Batas minimum stok sebelum peringatan</p>
                </div>

                <!-- Image -->
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                        Gambar Produk
                    </label>
                    <input type="file" id="image" name="image" accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('image') border-red-500 @enderror"
                        onchange="previewImage(event)">
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, GIF (Max: 2MB)</p>
                    <div id="imagePreview" class="mt-3 hidden">
                        <img src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg">
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="mt-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                Deskripsi
            </label>
            <textarea id="description" name="description" rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                placeholder="Deskripsi produk (opsional)">{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Product Attributes -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-tags mr-1"></i> Atribut Produk (Opsional)
            </label>
            <div class="bg-blue-50 border-l-4 border-blue-500 p-3 mb-3">
                <p class="text-sm text-blue-700">
                    <i class="fas fa-info-circle mr-1"></i>
                    Pilih template atribut yang sudah dibuat atau input manual atribut spesifik produk ini.
                </p>
            </div>
            <div id="attributes-container" class="space-y-2">
                <!-- Attribute rows will be added here -->
            </div>
            <button type="button" onclick="addAttributeRow()" class="mt-2 px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition text-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Atribut
            </button>
            <p class="text-xs text-gray-500 mt-2">
                <i class="fas fa-lightbulb mr-1"></i>
                <strong>Cara:</strong> Pilih template dari dropdown (jika ada) atau ketik manual nama atribut dan nilai
            </p>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
            <a href="{{ route('admin.products.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                <i class="fas fa-times mr-2"></i>
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-save mr-2"></i>
                Simpan Produk
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
                    <li>SKU akan dibuat otomatis jika tidak diisi</li>
                    <li>Stok awal dapat ditambahkan setelah produk dibuat melalui transaksi stok masuk</li>
                    <li>Pastikan harga jual lebih tinggi dari harga beli untuk mendapat profit</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let attributeIndex = 0;
const attributeTemplates = @json($attributes ?? []);
let selectedCategoryId = null;

// Listen to category change
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            selectedCategoryId = this.value;
            // Clear existing attributes when category changes
            const container = document.getElementById('attributes-container');
            if (container) {
                container.innerHTML = '';
            }
        });
        // Set initial category if exists
        selectedCategoryId = categorySelect.value;
    }
});

function addAttributeRow() {
    const container = document.getElementById('attributes-container');

    // Check if category is selected
    if (!selectedCategoryId) {
        alert('Silakan pilih kategori terlebih dahulu!');
        return;
    }

    const row = document.createElement('div');
    row.className = 'flex gap-2 items-start';

    // Build template options - filter by selected category
    let templateOptions = '<option value="">-- Input Manual --</option>';
    const filteredTemplates = attributeTemplates.filter(template =>
        template.category_id == selectedCategoryId
    );

    filteredTemplates.forEach(template => {
        templateOptions += `<option value="${template.id}" data-name="${template.name}">${template.name}</option>`;
    });

    row.innerHTML = `
        <select onchange="handleTemplateSelect(this, ${attributeIndex})"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm bg-white">
            ${templateOptions}
        </select>
        <input type="hidden" name="attributes[${attributeIndex}][attribute_id]" id="attr_template_${attributeIndex}">
        <input type="text" name="attributes[${attributeIndex}][name]" id="attr_name_${attributeIndex}"
               placeholder="Nama Atribut (contoh: Ukuran)"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
        <input type="text" name="attributes[${attributeIndex}][value]" placeholder="Nilai (contoh: XL)"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
        <button type="button" onclick="this.parentElement.remove()"
                class="px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition text-sm">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(row);
    attributeIndex++;
}

function handleTemplateSelect(selectElement, index) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const templateId = selectElement.value;
    const templateName = selectedOption.getAttribute('data-name');

    const nameInput = document.getElementById(`attr_name_${index}`);
    const templateIdInput = document.getElementById(`attr_template_${index}`);

    if (templateId) {
        // Template selected
        templateIdInput.value = templateId;
        nameInput.value = templateName;
        nameInput.readOnly = true;
        nameInput.classList.add('bg-gray-100');
    } else {
        // Manual input
        templateIdInput.value = '';
        nameInput.value = '';
        nameInput.readOnly = false;
        nameInput.classList.remove('bg-gray-100');
    }
}

function previewImage(event) {
    const preview = document.getElementById('imagePreview');
    const img = preview.querySelector('img');
    const file = event.target.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
@endsection
