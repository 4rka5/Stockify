@extends('layouts.admin')

@section('title', 'Laporan Stok')
@section('page-title', 'Laporan Stok')
@section('breadcrumb', 'Home / Laporan Stok')

@section('content')
<div class="mb-6 flex justify-between items-center no-print">
    <div>
        <h3 class="text-lg font-semibold text-gray-800">Laporan Stok Barang</h3>
        <p class="text-sm text-gray-600">Ringkasan stok semua produk dalam sistem</p>
    </div>
    <div class="flex gap-2">
        <button onclick="printReport()" id="printBtn" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition shadow-md hover:shadow-lg transform hover:scale-105">
            <i class="fas fa-print mr-2"></i>
            Cetak Laporan
        </button>
    </div>
</div>

<!-- Print Header (only visible when printing) -->
<div class="print-only mb-6" style="display: none;">
    <div class="text-center border-b-2 border-gray-800 pb-4 mb-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $appName ?? 'STOCKIFY - SISTEM MANAJEMEN STOK' }}</h2>
        <h3 class="text-xl font-semibold text-gray-700 mb-1">LAPORAN STOK BARANG</h3>
        <p class="text-sm text-gray-600 mt-2">Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
        <p class="text-sm text-gray-600 font-semibold">Dicetak oleh: {{ auth()->user()->name }} (Admin)</p>
    </div>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Produk</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $products->count() }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-box text-blue-500 text-2xl"></i>
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

<!-- Filter -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6 no-print">
    <form action="{{ route('admin.stock.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-folder mr-1"></i> Kategori
            </label>
            <select id="category" name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="supplier" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-truck mr-1"></i> Supplier
            </label>
            <select id="supplier" name="supplier" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Supplier</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ request('supplier') == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="stock_status" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-chart-bar mr-1"></i> Status Stok
            </label>
            <select id="stock_status" name="stock_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Status</option>
                <option value="empty" {{ request('stock_status') == 'empty' ? 'selected' : '' }}>Stok Habis</option>
                <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stok Rendah</option>
                <option value="normal" {{ request('stock_status') == 'normal' ? 'selected' : '' }}>Stok Normal</option>
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-search mr-2"></i>
                Filter
            </button>
            <a href="{{ route('admin.stock.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                <i class="fas fa-redo mr-2"></i>
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Export Buttons -->
<div class="flex justify-end gap-3 mb-6">
    <button onclick="window.print()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
        <i class="fas fa-print mr-2"></i>
        Cetak
    </button>
    <button onclick="exportToExcel()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">
        <i class="fas fa-file-excel mr-2"></i>
        Export Excel
    </button>
</div>

<!-- Stock Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="stockTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Tersedia</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Minimum</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Stok</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $index => $product)
                    @php
                        $currentStock = $product->current_stock;
                        $minimumStock = $product->minimum_stock;
                        $isLowStock = $currentStock <= $minimumStock;
                        $isEmpty = $currentStock == 0;
                        $stockValue = $currentStock * $product->purchase_price;
                    @endphp
                    <tr class="hover:bg-gray-50">
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            Rp {{ number_format($stockValue, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">Tidak ada data produk</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($products->count() > 0)
            <tfoot class="bg-gray-100">
                <tr>
                    <td colspan="5" class="px-6 py-4 text-right font-bold text-gray-900">Total Nilai Stok:</td>
                    <td colspan="4" class="px-6 py-4 text-left font-bold text-blue-600 text-lg">
                        Rp {{ number_format($products->sum(function($p) { return $p->current_stock * $p->purchase_price; }), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@push('scripts')
<script>
function exportToExcel() {
    // Simple CSV export
    let csv = [];
    let rows = document.querySelectorAll("#stockTable tr");

    for (let i = 0; i < rows.length - 1; i++) { // Exclude footer
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
    downloadLink.download = "Laporan_Stok_" + new Date().toISOString().slice(0,10) + ".csv";
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
}
</script>

<style>
    @media print {
        /* Hide non-printable elements */
        .no-print,
        nav,
        aside,
        .sidebar,
        button,
        .print-hidden,
        header,
        footer,
        .fixed,
        .sticky {
            display: none !important;
        }

        /* CRITICAL: Remove scrollbars and ensure full content visibility */
        * {
            overflow: visible !important;
            overflow-x: visible !important;
            overflow-y: visible !important;
        }

        html, body {
            overflow: visible !important;
            height: auto !important;
            width: 100% !important;
        }

        /* Adjust body and main container */
        body {
            margin: 0 !important;
            padding: 10px !important;
            font-size: 11px;
            background: white !important;
        }

        /* Show print header */
        .print-only {
            display: block !important;
        }

        /* Optimize page layout */
        .container,
        .main-content,
        main {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
        }

        /* Remove all scroll containers */
        .overflow-x-auto,
        .overflow-auto,
        .overflow-hidden {
            overflow: visible !important;
        }

        /* Remove margins */
        .mb-6, .mb-4 {
            margin-bottom: 0.8rem !important;
        }

        .mt-6, .mt-4 {
            margin-top: 0.5rem !important;
        }

        .p-6, .p-4 {
            padding: 0.8rem !important;
        }

        /* Table styling for print */
        table {
            page-break-inside: auto;
            border-collapse: collapse;
            width: 100% !important;
            font-size: 10px;
            border: 1px solid #000;
            table-layout: auto !important;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 6px 8px;
            word-wrap: break-word;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
            background: #f3f4f6 !important;
            font-weight: bold;
        }

        tbody {
            display: table-row-group;
        }

        /* Card styling */
        .bg-white,
        .rounded-lg,
        .shadow-md {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            border-radius: 0 !important;
            overflow: visible !important;
        }

        /* Stat cards styling */
        .grid {
            display: grid !important;
            gap: 0.5rem !important;
            width: 100% !important;
        }

        .grid > div {
            page-break-inside: avoid;
            border: 2px solid #333 !important;
            padding: 0.8rem !important;
        }

        /* Badge styling */
        .bg-green-100,
        .bg-red-100,
        .bg-yellow-100,
        .bg-blue-100 {
            border: 1px solid #333 !important;
            padding: 4px 8px !important;
            background: #f9f9f9 !important;
            color: #000 !important;
        }

        .text-green-800,
        .text-red-800,
        .text-yellow-800,
        .text-blue-800 {
            color: #000 !important;
        }

        /* Section headings */
        h3, h4 {
            page-break-after: avoid;
            font-weight: bold;
            margin-top: 0.8rem;
            margin-bottom: 0.5rem;
        }

        /* Better number formatting */
        .text-3xl {
            font-size: 1.5rem !important;
            font-weight: bold !important;
        }

        .text-lg {
            font-size: 1.1rem !important;
        }

        /* Optimize grid layout for print */
        .grid-cols-1,
        .md\\:grid-cols-2,
        .md\\:grid-cols-3,
        .md\\:grid-cols-4 {
            grid-template-columns: repeat(2, 1fr) !important;
        }

        /* Ensure images don't break layout */
        img {
            max-width: 50px !important;
            height: auto !important;
        }

        /* Remove fixed heights */
        .h-10, .h-full {
            height: auto !important;
        }
    }
</style>

<script>
    // Function to print the report with confirmation
    function printReport() {
        const confirmed = confirm('Apakah Anda yakin ingin mencetak laporan stok ini?');

        if (confirmed) {
            setTimeout(function() {
                window.print();
            }, 100);
        }
    }

    // Handle print button state
    window.onbeforeprint = function() {
        const btn = document.getElementById('printBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sedang Mencetak...';
        }
    };

    window.onafterprint = function() {
        const btn = document.getElementById('printBtn');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-print mr-2"></i>Cetak Laporan';
        }
    };
</script>
@endpush
@endsection
