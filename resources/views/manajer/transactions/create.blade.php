@extends('layouts.manajer')

@section('title', 'Buat Transaksi')
@section('page-title', 'Buat Transaksi Baru')
@section('breadcrumb', 'Home / Transaksi / Buat Baru')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Info Card -->
    <div class="bg-gradient-to-r from-teal-500 to-blue-500 rounded-lg shadow-lg p-6 mb-6 text-white">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-3xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold mb-2">Panduan Transaksi</h3>
                <ul class="text-sm space-y-1 opacity-90">
                    <li><i class="fas fa-arrow-down mr-2"></i><strong>Stok Masuk:</strong> Untuk menerima barang dari supplier atau menambah stok</li>
                    <li><i class="fas fa-arrow-up mr-2"></i><strong>Stok Keluar:</strong> Untuk mengeluarkan barang ke customer atau mengurangi stok</li>
                    <li><i class="fas fa-clipboard-check mr-2"></i><strong>Stock Opname:</strong> Untuk memeriksa dan menyesuaikan stok fisik dengan sistem</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-blue-50">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-plus-circle text-purple-500 mr-2"></i>
                Form Transaksi
            </h3>
            <p class="text-sm text-gray-600 mt-1">Pilih jenis transaksi dan isi form sesuai kebutuhan</p>
        </div>

        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Transaction Type Selector -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    <i class="fas fa-list mr-1"></i>
                    Jenis Transaksi <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button type="button" onclick="selectTransactionType('in')" id="btnTypeIn"
                            class="transaction-type-btn p-4 border-2 rounded-lg transition-all duration-200 hover:shadow-lg">
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-2">
                                <i class="fas fa-arrow-down text-green-600 text-xl"></i>
                            </div>
                            <span class="font-semibold text-gray-700">Stok Masuk</span>
                            <span class="text-xs text-gray-500 mt-1">Barang masuk gudang</span>
                        </div>
                    </button>

                    <button type="button" onclick="selectTransactionType('out')" id="btnTypeOut"
                            class="transaction-type-btn p-4 border-2 rounded-lg transition-all duration-200 hover:shadow-lg">
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-2">
                                <i class="fas fa-arrow-up text-red-600 text-xl"></i>
                            </div>
                            <span class="font-semibold text-gray-700">Stok Keluar</span>
                            <span class="text-xs text-gray-500 mt-1">Barang keluar gudang</span>
                        </div>
                    </button>

                    <button type="button" onclick="selectTransactionType('opname')" id="btnTypeOpname"
                            class="transaction-type-btn p-4 border-2 rounded-lg transition-all duration-200 hover:shadow-lg">
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mb-2">
                                <i class="fas fa-clipboard-check text-indigo-600 text-xl"></i>
                            </div>
                            <span class="font-semibold text-gray-700">Stock Opname</span>
                            <span class="text-xs text-gray-500 mt-1">Cek stok fisik</span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Dynamic Form Container -->
            <div id="formContainer" class="hidden">
                <!-- Form Stok Masuk -->
                <form id="formStokMasuk" action="{{ route('manajer.transactions.store') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="type" value="in">

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="product_id_in" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-box mr-1"></i>
                                    Produk <span class="text-red-500">*</span>
                                </label>
                                <select name="product_id" id="product_id_in" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->name }} ({{ $product->sku }}) - Stok: {{ $product->stock_quantity }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="supplier_id_in" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-truck mr-1"></i>
                                    Supplier (Opsional)
                                </label>
                                <select name="supplier_id" id="supplier_id_in"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">
                                            {{ $supplier->name }} - {{ $supplier->phone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="quantity_in" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-sort-numeric-up mr-1"></i>
                                    Jumlah <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="quantity" id="quantity_in" min="1" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                                       placeholder="0">
                            </div>

                            <div>
                                <label for="assigned_to_in" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user-check mr-1"></i>
                                    Tugaskan ke Staff <span class="text-red-500">*</span>
                                </label>
                                <select name="assigned_to" id="assigned_to_in" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                    <option value="">-- Pilih Staff --</option>
                                    @foreach($staffMembers as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Staff yang ditugaskan akan menerima dan memproses barang masuk</p>
                            </div>

                            <div class="md:col-span-2">
                                <label for="notes_in" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-sticky-note mr-1"></i>
                                    Catatan (Opsional)
                                </label>
                                <textarea name="notes" id="notes_in" rows="3"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                                          placeholder="Contoh: PO #12345, Urgent delivery, dll"></textarea>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="submit"
                                    class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors duration-200 flex items-center">
                                <i class="fas fa-save mr-2"></i>
                                Ajukan Stok Masuk
                            </button>
                            <button type="button" onclick="resetForm()"
                                    class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg transition-colors duration-200">
                                <i class="fas fa-redo mr-2"></i>
                                Reset
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Form Stok Keluar -->
                <form id="formStokKeluar" action="{{ route('manajer.transactions.store') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="type" value="out">

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="product_id_out" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-box mr-1"></i>
                                    Produk <span class="text-red-500">*</span>
                                </label>
                                <select name="product_id" id="product_id_out" required onchange="checkStockAvailability('out')"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-stock="{{ $product->stock_quantity }}">
                                            {{ $product->name }} ({{ $product->sku }}) - Stok: {{ $product->stock_quantity }}
                                        </option>
                                    @endforeach
                                </select>
                                <p id="stockWarning_out" class="text-sm text-gray-600 mt-1 hidden">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Stok tersedia: <span id="availableStock_out" class="font-bold">0</span> unit
                                </p>
                            </div>

                            <div>
                                <label for="supplier_id_out" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-truck mr-1"></i>
                                    Tujuan/Customer (Opsional)
                                </label>
                                <select name="supplier_id" id="supplier_id_out"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                                    <option value="">-- Pilih Tujuan --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">
                                            {{ $supplier->name }} - {{ $supplier->phone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="quantity_out" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-sort-numeric-down mr-1"></i>
                                    Jumlah <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="quantity" id="quantity_out" min="1" required onchange="validateStockOut()"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                                       placeholder="0">
                                <p id="quantityError_out" class="text-red-500 text-xs mt-1 hidden">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Jumlah melebihi stok tersedia!
                                </p>
                            </div>

                            <div>
                                <label for="assigned_to_out" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user-check mr-1"></i>
                                    Tugaskan ke Staff <span class="text-red-500">*</span>
                                </label>
                                <select name="assigned_to" id="assigned_to_out" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                                    <option value="">-- Pilih Staff --</option>
                                    @foreach($staffMembers as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Staff yang ditugaskan akan memproses pengeluaran barang</p>
                            </div>

                            <div class="md:col-span-2">
                                <label for="notes_out" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-sticky-note mr-1"></i>
                                    Catatan (Opsional)
                                </label>
                                <textarea name="notes" id="notes_out" rows="3"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                                          placeholder="Contoh: SO #54321, Kirim ke cabang Jakarta, dll"></textarea>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="submit"
                                    class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors duration-200 flex items-center">
                                <i class="fas fa-save mr-2"></i>
                                Ajukan Stok Keluar
                            </button>
                            <button type="button" onclick="resetForm()"
                                    class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg transition-colors duration-200">
                                <i class="fas fa-redo mr-2"></i>
                                Reset
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Form Stock Opname -->
                <form id="formStockOpname" action="{{ route('manajer.transactions.store') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="type" value="opname">

                    <div class="space-y-4">
                        <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-indigo-500 text-lg"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-indigo-700">
                                        <strong>Stock Opname:</strong> Tugaskan staff untuk memeriksa stok fisik produk dan melaporkan hasilnya.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="product_id_opname" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-box mr-1"></i>
                                    Produk <span class="text-red-500">*</span>
                                </label>
                                <select name="product_id" id="product_id_opname" required onchange="showSystemStock()"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-stock="{{ $product->stock_quantity }}">
                                            {{ $product->name }} ({{ $product->sku }}) - Stok Sistem: {{ $product->stock_quantity }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="systemStockInfo" class="mt-2 p-3 bg-blue-50 rounded-lg border border-blue-200 hidden">
                                    <p class="text-sm text-blue-700">
                                        <i class="fas fa-database mr-1"></i>
                                        Stok di Sistem: <span id="systemStockValue" class="font-bold">0</span> unit
                                    </p>
                                </div>
                            </div>

                            <div>
                                <label for="assigned_to_opname" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user-check mr-1"></i>
                                    Tugaskan ke Staff <span class="text-red-500">*</span>
                                </label>
                                <select name="assigned_to" id="assigned_to_opname" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    <option value="">-- Pilih Staff --</option>
                                    @foreach($staffMembers as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Staff akan melakukan pengecekan stok fisik</p>
                            </div>

                            <div class="md:col-span-2">
                                <label for="notes_opname" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-sticky-note mr-1"></i>
                                    Catatan untuk Staff (Opsional)
                                </label>
                                <textarea name="notes" id="notes_opname" rows="3"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                          placeholder="Contoh: Periksa di gudang A rak 3, Fokus pada produk rusak, dll"></textarea>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="submit"
                                    class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors duration-200 flex items-center">
                                <i class="fas fa-save mr-2"></i>
                                Tugaskan Stock Opname
                            </button>
                            <button type="button" onclick="resetForm()"
                                    class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg transition-colors duration-200">
                                <i class="fas fa-redo mr-2"></i>
                                Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Placeholder when no type selected -->
            <div id="placeholderMessage" class="text-center py-12">
                <i class="fas fa-hand-pointer text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-500 text-lg">Pilih jenis transaksi di atas untuk memulai</p>
            </div>
        </div>
    </div>
</div>

<script>
let selectedType = null;

function selectTransactionType(type) {
    selectedType = type;

    // Reset all buttons
    document.querySelectorAll('.transaction-type-btn').forEach(btn => {
        btn.classList.remove('border-green-500', 'border-red-500', 'border-indigo-500', 'bg-green-50', 'bg-red-50', 'bg-indigo-50', 'shadow-lg');
        btn.classList.add('border-gray-300');
    });

    // Hide all forms
    document.getElementById('formStokMasuk').classList.add('hidden');
    document.getElementById('formStokKeluar').classList.add('hidden');
    document.getElementById('formStockOpname').classList.add('hidden');
    document.getElementById('placeholderMessage').classList.add('hidden');
    document.getElementById('formContainer').classList.remove('hidden');

    // Show selected form and highlight button
    if (type === 'in') {
        document.getElementById('btnTypeIn').classList.add('border-green-500', 'bg-green-50', 'shadow-lg');
        document.getElementById('btnTypeIn').classList.remove('border-gray-300');
        document.getElementById('formStokMasuk').classList.remove('hidden');
    } else if (type === 'out') {
        document.getElementById('btnTypeOut').classList.add('border-red-500', 'bg-red-50', 'shadow-lg');
        document.getElementById('btnTypeOut').classList.remove('border-gray-300');
        document.getElementById('formStokKeluar').classList.remove('hidden');
    } else if (type === 'opname') {
        document.getElementById('btnTypeOpname').classList.add('border-indigo-500', 'bg-indigo-50', 'shadow-lg');
        document.getElementById('btnTypeOpname').classList.remove('border-gray-300');
        document.getElementById('formStockOpname').classList.remove('hidden');
    }
}

function resetForm() {
    if (confirm('Apakah Anda yakin ingin mereset form?')) {
        if (selectedType === 'in') {
            document.getElementById('formStokMasuk').reset();
        } else if (selectedType === 'out') {
            document.getElementById('formStokKeluar').reset();
            document.getElementById('stockWarning_out').classList.add('hidden');
            document.getElementById('quantityError_out').classList.add('hidden');
        } else if (selectedType === 'opname') {
            document.getElementById('formStockOpname').reset();
            document.getElementById('systemStockInfo').classList.add('hidden');
        }
    }
}

function checkStockAvailability(type) {
    const select = document.getElementById(`product_id_${type}`);
    const selectedOption = select.options[select.selectedIndex];
    const stock = selectedOption.getAttribute('data-stock');

    if (stock) {
        document.getElementById(`stockWarning_${type}`).classList.remove('hidden');
        document.getElementById(`availableStock_${type}`).textContent = stock;
    }
}

function validateStockOut() {
    const select = document.getElementById('product_id_out');
    const selectedOption = select.options[select.selectedIndex];
    const availableStock = parseInt(selectedOption.getAttribute('data-stock') || 0);
    const quantity = parseInt(document.getElementById('quantity_out').value || 0);
    const errorMsg = document.getElementById('quantityError_out');

    if (quantity > availableStock) {
        errorMsg.classList.remove('hidden');
    } else {
        errorMsg.classList.add('hidden');
    }
}

function showSystemStock() {
    const select = document.getElementById('product_id_opname');
    const selectedOption = select.options[select.selectedIndex];
    const stock = selectedOption.getAttribute('data-stock');

    if (stock) {
        document.getElementById('systemStockInfo').classList.remove('hidden');
        document.getElementById('systemStockValue').textContent = stock;
    } else {
        document.getElementById('systemStockInfo').classList.add('hidden');
    }
}
</script>
@endsection
