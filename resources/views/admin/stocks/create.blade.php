@extends('layouts.admin')

@section('title', 'Tambah Transaksi Stok')
@section('page-title', 'Tambah Transaksi Stok')
@section('breadcrumb', 'Home / Transaksi Stok / Tambah')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.stock-transactions.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Daftar Transaksi
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-6">
        <h3 class="text-xl font-semibold text-gray-800">Form Tambah Transaksi Stok</h3>
        <p class="text-sm text-gray-600 mt-1">Lengkapi form di bawah ini untuk menambah transaksi stok</p>
    </div>

    <form action="{{ route('admin.stock-transactions.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Product -->
                <div>
                    <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Produk <span class="text-red-500">*</span>
                    </label>
                    <select id="product_id" name="product_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('product_id') border-red-500 @enderror"
                        required onchange="updateProductInfo()">
                        <option value="">Pilih Produk</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                data-sku="{{ $product->sku }}"
                                data-current-stock="{{ $product->current_stock }}"
                                data-minimum-stock="{{ $product->minimum_stock }}"
                                {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} (Stok: {{ $product->current_stock }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Product Info Display -->
                <div id="productInfo" class="hidden p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Informasi Produk</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">SKU:</span>
                            <span id="productSku" class="font-semibold text-gray-800">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Stok Saat Ini:</span>
                            <span id="productStock" class="font-semibold text-gray-800">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Stok Minimum:</span>
                            <span id="productMinStock" class="font-semibold text-gray-800">-</span>
                        </div>
                    </div>
                </div>
                
                <!-- Stock Warning Alert -->
                <div id="stockWarning" class="hidden p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Peringatan:</strong> Pastikan quantity tidak melebihi stok tersedia saat melakukan transaksi keluar.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Transaksi <span class="text-red-500">*</span>
                    </label>
                    <select id="type" name="type"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror"
                        required>
                        <option value="">Pilih Tipe</option>
                        <option value="masuk" {{ old('type') == 'masuk' ? 'selected' : '' }}>Stok Masuk</option>
                        <option value="keluar" {{ old('type') == 'keluar' ? 'selected' : '' }}>Stok Keluar</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Pilih "Stok Masuk" untuk penambahan stok atau "Stok Keluar" untuk pengurangan stok
                    </p>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Quantity -->
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="quantity" name="quantity" value="{{ old('quantity') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('quantity') border-red-500 @enderror"
                        placeholder="Masukkan jumlah"
                        min="1"
                        required>
                    @error('quantity')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Date -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Transaksi <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" id="date" name="date" value="{{ old('date', now()->format('Y-m-d\TH:i')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('date') border-red-500 @enderror"
                        required>
                    @error('date')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan
                    </label>
                    <textarea id="notes" name="notes" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                        placeholder="Catatan transaksi (opsional)">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
            <a href="{{ route('admin.stock-transactions.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                <i class="fas fa-times mr-2"></i>
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-save mr-2"></i>
                Simpan Transaksi
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
                    <li><strong>Stok Masuk:</strong> Digunakan ketika menerima barang dari supplier atau retur dari customer</li>
                    <li><strong>Stok Keluar:</strong> Digunakan ketika mengeluarkan barang untuk penjualan atau keperluan lain</li>
                    <li>Transaksi akan langsung mempengaruhi stok produk</li>
                    <li>Pastikan jumlah stok keluar tidak melebihi stok yang tersedia</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentStock = 0;

function updateProductInfo() {
    const select = document.getElementById('product_id');
    const selectedOption = select.options[select.selectedIndex];
    const productInfo = document.getElementById('productInfo');

    if (select.value) {
        const sku = selectedOption.getAttribute('data-sku');
        currentStock = parseInt(selectedOption.getAttribute('data-current-stock'));
        const minimumStock = selectedOption.getAttribute('data-minimum-stock');

        document.getElementById('productSku').textContent = sku;
        document.getElementById('productStock').textContent = currentStock + ' unit';
        document.getElementById('productMinStock').textContent = minimumStock + ' unit';

        productInfo.classList.remove('hidden');
        
        // Check type to show warning
        checkStockWarning();
    } else {
        productInfo.classList.add('hidden');
        document.getElementById('stockWarning').classList.add('hidden');
    }
}

function checkStockWarning() {
    const typeSelect = document.getElementById('type');
    const quantityInput = document.getElementById('quantity');
    const stockWarning = document.getElementById('stockWarning');
    
    if (typeSelect.value === 'keluar') {
        const quantity = parseInt(quantityInput.value) || 0;
        
        if (quantity > currentStock) {
            stockWarning.classList.remove('hidden');
            stockWarning.classList.remove('bg-yellow-50', 'border-yellow-200');
            stockWarning.classList.add('bg-red-50', 'border-red-200');
            stockWarning.querySelector('.text-yellow-700').classList.remove('text-yellow-700');
            stockWarning.querySelector('.text-yellow-700, .text-red-700').classList.add('text-red-700');
            stockWarning.querySelector('strong').textContent = 'Error:';
            stockWarning.querySelector('p').innerHTML = '<strong>Error:</strong> Quantity melebihi stok tersedia! Stok tersedia: ' + currentStock + ' unit.';
        } else {
            stockWarning.classList.remove('hidden', 'bg-red-50', 'border-red-200');
            stockWarning.classList.add('bg-yellow-50', 'border-yellow-200');
            stockWarning.querySelector('.text-red-700').classList.remove('text-red-700');
            stockWarning.querySelector('.text-yellow-700, p').classList.add('text-yellow-700');
            stockWarning.querySelector('p').innerHTML = '<strong>Peringatan:</strong> Pastikan quantity tidak melebihi stok tersedia saat melakukan transaksi keluar.';
        }
    } else {
        stockWarning.classList.add('hidden');
    }
}

// Call on page load if product is already selected
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('product_id');
    const typeSelect = document.getElementById('type');
    const quantityInput = document.getElementById('quantity');
    
    if (select.value) {
        updateProductInfo();
    }
    
    // Add event listeners
    typeSelect.addEventListener('change', checkStockWarning);
    quantityInput.addEventListener('input', checkStockWarning);
});
</script>
@endpush
@endsection
