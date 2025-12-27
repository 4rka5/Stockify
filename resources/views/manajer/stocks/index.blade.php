@extends('layouts.manajer')

@section('title', 'Monitor Stok')
@section('page-title', 'Monitor Stok')
@section('breadcrumb', 'Home / Monitor Stok')

@section('content')
<div class="mb-6">
    <h3 class="text-lg font-semibold text-gray-800">Monitor Stok Barang</h3>
    <p class="text-sm text-gray-600">Pantau dan kelola stok produk dalam sistem</p>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-teal-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Produk</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $products->count() }}</p>
            </div>
            <div class="bg-teal-100 rounded-full p-3">
                <i class="fas fa-box text-teal-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Stok</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">
                    {{ $products->sum(function($product) { return $product->current_stock; }) }}
                </p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-cubes text-green-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Stok Rendah</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">
                    {{ $products->filter(function($p) { return $p->current_stock > 0 && $p->current_stock <= $p->minimum_stock; })->count() }}
                </p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Stok Habis</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">
                    {{ $products->filter(function($p) { return $p->current_stock == 0; })->count() }}
                </p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-times-circle text-red-500 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Stock Actions -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Stock In Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-arrow-down text-green-500 mr-2"></i>
            Tambah Stok Masuk
        </h4>
        <form action="{{ route('manajer.stock.in.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="product_id_in" class="block text-sm font-medium text-gray-700 mb-2">Pilih Produk</label>
                <select id="product_id_in" name="product_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="quantity_in" class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                <input type="number" id="quantity_in" name="quantity" min="1" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
            </div>
            <div class="mb-4">
                <label for="assigned_to_in" class="block text-sm font-medium text-gray-700 mb-2">Tugaskan ke Staff <span class="text-red-500">*</span></label>
                <select id="assigned_to_in" name="assigned_to" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    <option value="">-- Pilih Staff --</option>
                    @foreach($staffMembers as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Staff yang ditugaskan akan menerima dan memproses barang masuk ini</p>
            </div>
            <div class="mb-4">
                <label for="notes_in" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea id="notes_in" name="notes" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"></textarea>
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>
                Ajukan Stok Masuk
            </button>
        </form>
    </div>

    <!-- Stock Out Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-arrow-up text-red-500 mr-2"></i>
            Tambah Stok Keluar
        </h4>
        <form action="{{ route('manajer.stock.out.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="product_id_out" class="block text-sm font-medium text-gray-700 mb-2">Pilih Produk</label>
                <select id="product_id_out" name="product_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-stock="{{ $product->current_stock }}">
                            {{ $product->name }} (Stok: {{ $product->current_stock }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="quantity_out" class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                <input type="number" id="quantity_out" name="quantity" min="1" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
            </div>
            <div class="mb-4">
                <label for="assigned_to_out" class="block text-sm font-medium text-gray-700 mb-2">Tugaskan ke Staff <span class="text-red-500">*</span></label>
                <select id="assigned_to_out" name="assigned_to" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    <option value="">-- Pilih Staff --</option>
                    @foreach($staffMembers as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Staff yang ditugaskan akan menyiapkan dan memproses barang keluar ini</p>
            </div>
            <div class="mb-4">
                <label for="notes_out" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea id="notes_out" name="notes" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"></textarea>
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                <i class="fas fa-minus mr-2"></i>
                Ajukan Stok Keluar
            </button>
        </form>
    </div>
</div>

<!-- Export Buttons & Actions -->
<div class="flex justify-between items-center mb-6">
    <button onclick="openAssignOpnameModal()" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition no-print">
        <i class="fas fa-clipboard-check mr-2"></i>
        Tugaskan Cek Stok Fisik
    </button>
    <div class="flex gap-3">
        <button onclick="window.print()" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition no-print">
            <i class="fas fa-print mr-2"></i>
            Cetak
        </button>
        <button onclick="exportToExcel()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition no-print">
            <i class="fas fa-file-excel mr-2"></i>
            Export Excel
        </button>
    </div>
</div>

<!-- Stock Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="stockTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <input type="checkbox" id="selectAll" class="rounded">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Tersedia</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Minimum</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $index => $product)
                    @php
                        $currentStock = $product->current_stock;
                        $minimumStock = $product->minimum_stock;
                        $isLowStock = $currentStock <= $minimumStock;
                        $isEmpty = $currentStock == 0;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <input type="checkbox" class="product-checkbox rounded" value="{{ $product->id }}" data-product-name="{{ $product->name }}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($product->image)
                                        <img class="h-10 w-10 rounded-lg object-cover" src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-box text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $product->sku }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ $product->category->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $product->supplier->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="text-lg font-bold {{ $isEmpty ? 'text-red-600' : ($isLowStock ? 'text-yellow-600' : 'text-green-600') }}">
                                {{ $currentStock }}
                            </span>
                            <span class="text-sm text-gray-500">unit</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                            {{ $minimumStock }} unit
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($isEmpty)
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Habis
                                </span>
                            @elseif($isLowStock)
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Rendah
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Normal
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">Tidak ada data produk</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
function exportToExcel() {
    // Simple CSV export
    let csv = [];
    let rows = document.querySelectorAll("#stockTable tr");

    for (let i = 0; i < rows.length; i++) {
        let row = [], cols = rows[i].querySelectorAll("td, th");

        for (let j = 0; j < cols.length; j++) {
            let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s)/gm, ' ')
            data = data.replace(/"/g, '""');
            row.push('"' + data + '"');
        }

        csv.push(row.join(","));
    }

    let csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
    let downloadLink = document.createElement("a");
    downloadLink.download = "Monitor_Stok_" + new Date().toISOString().slice(0,10) + ".csv";
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
}

// Validate stock out quantity
document.getElementById('product_id_out').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const availableStock = selectedOption.getAttribute('data-stock');
    const quantityInput = document.getElementById('quantity_out');

    if (availableStock) {
        quantityInput.max = availableStock;
    }
});

document.getElementById('quantity_out').addEventListener('input', function() {
    const selectedProduct = document.getElementById('product_id_out');
    const selectedOption = selectedProduct.options[selectedProduct.selectedIndex];
    const availableStock = parseInt(selectedOption.getAttribute('data-stock'));
    const inputValue = parseInt(this.value);

    if (inputValue > availableStock) {
        this.setCustomValidity(`Stok tidak mencukupi. Maksimal: ${availableStock}`);
    } else {
        this.setCustomValidity('');
    }
});

// Select All functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

function openAssignOpnameModal() {
    const selectedCheckboxes = document.querySelectorAll('.product-checkbox:checked');

    if (selectedCheckboxes.length === 0) {
        alert('Pilih minimal satu produk untuk ditugaskan');
        return;
    }

    const productIds = [];
    const productNames = [];

    selectedCheckboxes.forEach(cb => {
        productIds.push(cb.value);
        productNames.push(cb.dataset.productName);
    });

    // Update selected products list
    const listHtml = productNames.map((name, index) =>
        `<div class="text-sm text-gray-700 py-1">
            <i class="fas fa-check-circle text-green-500 mr-2"></i>${index + 1}. ${name}
            <input type="hidden" name="product_ids[]" value="${productIds[index]}">
        </div>`
    ).join('');

    document.getElementById('selectedProductsList').innerHTML = listHtml;
    document.getElementById('assignOpnameModal').classList.remove('hidden');
}

function closeAssignOpnameModal() {
    document.getElementById('assignOpnameModal').classList.add('hidden');
    document.getElementById('notes_opname').value = '';
    document.getElementById('assigned_to_opname').value = '';
}

// Close modal on outside click
document.getElementById('assignOpnameModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAssignOpnameModal();
    }
});
</script>
@endpush

<!-- Assign Stock Opname Modal -->
<div id="assignOpnameModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-clipboard-check text-purple-500 mr-2"></i>
                Tugaskan Cek Stok Fisik
            </h3>
            <form action="{{ route('manajer.stock.assign-opname') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Produk yang Dipilih</label>
                    <div id="selectedProductsList" class="max-h-40 overflow-y-auto border rounded-lg p-3 bg-gray-50">
                        <p class="text-sm text-gray-500">Belum ada produk yang dipilih</p>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="assigned_to_opname" class="block text-sm font-medium text-gray-700 mb-2">Tugaskan ke Staff</label>
                    <select id="assigned_to_opname" name="assigned_to" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Pilih Staff</option>
                        @foreach($staffMembers as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="notes_opname" class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea id="notes_opname" name="notes" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                              placeholder="Instruksi khusus untuk staff..."></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeAssignOpnameModal()" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Tugaskan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }

    body * {
        visibility: hidden;
    }

    #stockTable, #stockTable * {
        visibility: visible;
    }

    #stockTable {
        position: absolute;
        left: 0;
        top: 0;
    }
}
</style>
@endsection
