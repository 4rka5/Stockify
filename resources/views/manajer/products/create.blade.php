@extends('layouts.manajer')

@section('title', 'Tambah Produk Baru')
@section('page-title', 'Tambah Produk Baru')
@section('breadcrumb', 'Home / Produk / Tambah')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('manajer.products.index') }}" class="inline-flex items-center text-teal-600 hover:text-teal-800">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar Produk
        </a>
    </div>

    <!-- Info Alert -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500 text-lg"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Catatan:</strong> Produk yang Anda tambahkan akan diajukan ke admin untuk disetujui.
                    Produk akan muncul di daftar setelah admin menyetujui pengajuan Anda.
                </p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-teal-50 to-blue-50">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-box text-teal-500 mr-2"></i>
                Form Pengajuan Produk Baru
            </h3>
        </div>

        <form action="{{ route('manajer.products.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Produk <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                           placeholder="Contoh: Laptop Asus ROG">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SKU -->
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">
                        SKU <span class="text-gray-500 text-xs">(Opsional - Auto-generate jika kosong)</span>
                    </label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                           placeholder="Contoh: LPT-001 (Kosongkan untuk auto-generate)">
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Jika dikosongkan, SKU akan dibuat otomatis
                    </p>
                    @error('sku')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" id="category_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Supplier -->
                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Supplier <span class="text-red-500">*</span>
                    </label>
                    <select name="supplier_id" id="supplier_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purchase Price -->
                <div>
                    <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-2">
                        Harga Beli <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}" required min="0" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                           placeholder="0">
                    @error('purchase_price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Selling Price -->
                <div>
                    <label for="selling_price" class="block text-sm font-medium text-gray-700 mb-2">
                        Harga Jual <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price') }}" required min="0" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                           placeholder="0">
                    @error('selling_price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Minimum Stock -->
                <div>
                    <label for="minimum_stock" class="block text-sm font-medium text-gray-700 mb-2">
                        Stok Minimum <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="minimum_stock" id="minimum_stock" value="{{ old('minimum_stock') }}" required min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                           placeholder="0">
                    @error('minimum_stock')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image -->
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                        Gambar Produk (Opsional)
                    </label>
                    <input type="file" name="image" id="image" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB</p>
                    @error('image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi (Opsional)
                    </label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                              placeholder="Deskripsi produk...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Product Attributes -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tags mr-1"></i> Atribut Produk (Opsional)
                    </label>
                    <div id="attributes-container" class="space-y-2">
                        <!-- Attribute rows will be added here -->
                    </div>
                    <button type="button" onclick="addAttributeRow()" class="mt-2 px-4 py-2 bg-teal-100 hover:bg-teal-200 text-teal-700 rounded-lg transition text-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah Atribut
                    </button>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Contoh: Warna - Merah, Ukuran - XL, Bahan - Katun
                    </p>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 my-6"></div>

<script>
let attributeIndex = 0;

function addAttributeRow() {
    const container = document.getElementById('attributes-container');
    const row = document.createElement('div');
    row.className = 'flex gap-2 items-start';
    row.innerHTML = `
        <input type="text" name="attributes[${attributeIndex}][name]" placeholder="Nama (contoh: Warna)"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 text-sm">
        <input type="text" name="attributes[${attributeIndex}][value]" placeholder="Nilai (contoh: Merah)"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 text-sm">
        <button type="button" onclick="this.parentElement.remove()"
                class="px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition text-sm">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(row);
    attributeIndex++;
}

// Add one attribute row by default
document.addEventListener('DOMContentLoaded', function() {
    addAttributeRow();
});
</script>

            <!-- Task Assignment Section (Optional) -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-tasks text-teal-500 mr-2"></i>
                        Tugaskan Staff (Opsional)
                    </h4>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="toggle_task" name="create_task" value="1"
                               class="form-checkbox h-5 w-5 text-teal-600 rounded focus:ring-teal-500"
                               onchange="toggleTaskSection()">
                        <span class="ml-2 text-sm text-gray-700">Buat tugas transaksi</span>
                    </label>
                </div>

                <div id="task_section" class="hidden bg-teal-50 border border-teal-200 rounded-lg p-4">
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-3 mb-4">
                        <p class="text-sm text-blue-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Catatan:</strong> Staff akan menerima notifikasi tugas <strong>setelah admin menyetujui</strong> produk ini.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Task Type -->
                        <div>
                            <label for="task_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Transaksi <span class="text-red-500">*</span>
                            </label>
                            <select name="task_type" id="task_type"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                <option value="">-- Pilih Jenis --</option>
                                <option value="in" {{ old('task_type') == 'in' ? 'selected' : '' }}>Barang Masuk</option>
                                <option value="out" {{ old('task_type') == 'out' ? 'selected' : '' }}>Barang Keluar</option>
                            </select>
                            @error('task_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Assigned Staff -->
                        <div>
                            <label for="assigned_staff_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Staff yang Ditugaskan <span class="text-red-500">*</span>
                            </label>
                            <select name="assigned_staff_id" id="assigned_staff_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                <option value="">-- Pilih Staff --</option>
                                @foreach($staffUsers as $staff)
                                    <option value="{{ $staff->id }}" {{ old('assigned_staff_id') == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_staff_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Task Quantity -->
                        <div>
                            <label for="task_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                Jumlah <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="task_quantity" id="task_quantity" value="{{ old('task_quantity', 1) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                   placeholder="1">
                            @error('task_quantity')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Task Notes -->
                        <div>
                            <label for="task_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan Tugas (Opsional)
                            </label>
                            <textarea name="task_notes" id="task_notes" rows="2"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                      placeholder="Catatan untuk staff...">{{ old('task_notes') }}</textarea>
                            @error('task_notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Ajukan Produk
                </button>
                <a href="{{ route('manajer.products.index') }}" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleTaskSection() {
    const checkbox = document.getElementById('toggle_task');
    const section = document.getElementById('task_section');
    const taskType = document.getElementById('task_type');
    const staffId = document.getElementById('assigned_staff_id');
    const quantity = document.getElementById('task_quantity');

    if (checkbox.checked) {
        section.classList.remove('hidden');
        // Make fields required with validation
        taskType.setAttribute('required', 'required');
        staffId.setAttribute('required', 'required');
        quantity.setAttribute('required', 'required');
        quantity.setAttribute('min', '1');
    } else {
        section.classList.add('hidden');
        // Remove all validation when hidden
        taskType.removeAttribute('required');
        staffId.removeAttribute('required');
        quantity.removeAttribute('required');
        quantity.removeAttribute('min');
        // Clear values to prevent submission
        taskType.value = '';
        staffId.value = '';
        quantity.value = '';
    }
}

// Check on page load if old input exists
document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('toggle_task');
    if (checkbox.checked) {
        toggleTaskSection();
    }
});
</script>
@endpush
