@extends('layouts.admin')

@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')
@section('breadcrumb', 'Home / Produk / Edit')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.products.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Daftar Produk
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-6">
        <h3 class="text-xl font-semibold text-gray-800">Form Edit Produk</h3>
        <p class="text-sm text-gray-600 mt-1">Perbarui informasi produk</p>
    </div>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Produk <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
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
                        SKU (Stock Keeping Unit) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="sku" name="sku" value="{{ old('sku', $product->sku) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('sku') border-red-500 @enderror"
                        required>
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
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
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>
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
                        <input type="number" id="purchase_price" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}"
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
                        <input type="number" id="selling_price" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}"
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
                    <input type="number" id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', $product->minimum_stock) }}"
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
                    @if($product->image)
                        <div class="mb-3">
                            <p class="text-sm text-gray-600 mb-2">Gambar saat ini:</p>
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-lg">
                        </div>
                    @endif
                    <input type="file" id="image" name="image" accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('image') border-red-500 @enderror"
                        onchange="previewImage(event)">
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, GIF (Max: 2MB). Kosongkan jika tidak ingin mengubah</p>
                    <div id="imagePreview" class="mt-3 hidden">
                        <p class="text-sm text-gray-600 mb-2">Preview:</p>
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
                placeholder="Deskripsi produk (opsional)">{{ old('description', $product->description) }}</textarea>
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
                    Kelola atribut spesifik produk seperti ukuran, warna, berat, atau spesifikasi lainnya.
                </p>
            </div>
            <div id="attributes-container" class="space-y-2">
                @foreach($product->attributes as $index => $attribute)
                <div class="flex gap-2 items-start">
                    <select onchange="handleTemplateSelect(this, {{ $index }})" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm bg-white">
                        <option value="">-- Input Manual --</option>
                        @foreach($attributes->where('category_id', $product->category_id) as $attrTemplate)
                            <option value="{{ $attrTemplate->id }}" data-name="{{ $attrTemplate->name }}" {{ $attribute->attribute_id == $attrTemplate->id ? 'selected' : '' }}>
                                {{ $attrTemplate->name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="attributes[{{ $index }}][attribute_id]" id="attr_template_{{ $index }}" value="{{ old('attributes.'.$index.'.attribute_id', $attribute->attribute_id) }}">
                    <input type="text" name="attributes[{{ $index }}][name]" id="attr_name_{{ $index }}" value="{{ old('attributes.'.$index.'.name', $attribute->name) }}" placeholder="Nama Atribut (contoh: Ukuran)"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm {{ $attribute->attribute_id ? 'bg-gray-100' : '' }}" {{ $attribute->attribute_id ? 'readonly' : '' }}>
                    <input type="text" name="attributes[{{ $index }}][value]" value="{{ old('attributes.'.$index.'.value', $attribute->value) }}" placeholder="Nilai (contoh: XL)"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    <button type="button" onclick="this.parentElement.remove()"
                            class="px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition text-sm">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                @endforeach
            </div>
            <button type="button" onclick="addAttributeRow()" class="mt-2 px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition text-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Atribut
            </button>
            <p class="text-xs text-gray-500 mt-2">
                <i class="fas fa-lightbulb mr-1"></i>
                Pilih dari template atau input manual. Template hanya menampilkan atribut untuk kategori yang dipilih.
            </p>
        </div>

        <!-- Product Info -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Informasi Stok</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">Stok Saat Ini:</span>
                    <span class="font-semibold text-gray-800 ml-2">{{ $product->current_stock }} unit</span>
                </div>
                <div>
                    <span class="text-gray-600">Total Transaksi:</span>
                    <span class="font-semibold text-gray-800 ml-2">{{ $product->stockTransactions->count() }} transaksi</span>
                </div>
                <div>
                    <span class="text-gray-600">Status:</span>
                    @if($product->current_stock <= $product->minimum_stock)
                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                            Stok Rendah
                        </span>
                    @else
                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Stok Aman
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
            <a href="{{ route('admin.products.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                <i class="fas fa-times mr-2"></i>
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-save mr-2"></i>
                Update Produk
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
                    <li>Perubahan harga tidak akan mempengaruhi transaksi yang sudah ada</li>
                    <li>Untuk mengubah stok, gunakan menu Transaksi Stok</li>
                    <li>Gambar lama akan terhapus jika Anda upload gambar baru</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let attributeIndex = {{ $product->attributes->count() }};
const attributeTemplates = @json($attributes ?? []);
let selectedCategoryId = {{ $product->category_id }};

// Listen to category change
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            const oldCategoryId = selectedCategoryId;
            selectedCategoryId = this.value;

            // Warn user if changing category with existing attributes
            const container = document.getElementById('attributes-container');
            if (container && container.children.length > 0 && oldCategoryId != selectedCategoryId) {
                if (confirm('Mengubah kategori akan mereset atribut yang sudah ada. Lanjutkan?')) {
                    container.innerHTML = '';
                } else {
                    // Revert category selection
                    this.value = oldCategoryId;
                    selectedCategoryId = oldCategoryId;
                }
            }
        });
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
