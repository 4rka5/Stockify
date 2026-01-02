@extends('layouts.manajer')

@section('title', 'Laporan Stok')
@section('page-title', 'Laporan Stok')
@section('breadcrumb', 'Home / Laporan')

@section('content')
<div class="mb-6 flex justify-between items-center no-print">
    <div>
        <h3 class="text-lg font-semibold text-gray-800">Laporan Stok Barang</h3>
        <p class="text-sm text-gray-600">Ringkasan dan analisis data stok</p>
    </div>
    <div class="flex gap-2">
        <button onclick="printReport()" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition shadow-md hover:shadow-lg transform hover:scale-105">
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
        <p class="text-sm text-gray-600 mt-2">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        <p class="text-sm text-gray-600">Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
        <p class="text-sm text-gray-600 font-semibold">Dicetak oleh: {{ auth()->user()->name }} (Manajer)</p>
    </div>
</div>

<!-- Quick Filter Buttons -->
<div class="bg-white rounded-lg shadow-md p-4 mb-4 no-print">
    <div class="flex items-center gap-2 flex-wrap">
        <span class="text-sm font-medium text-gray-700">Filter Cepat:</span>
        <a href="{{ route('manajer.reports.index', ['filter' => 'today']) }}"
           class="quick-filter px-4 py-2 {{ request('filter') == 'today' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} rounded-lg transition text-sm">
            <i class="fas fa-calendar-day mr-1"></i>
            Hari Ini
        </a>
        <a href="{{ route('manajer.reports.index', ['filter' => 'week']) }}"
           class="quick-filter px-4 py-2 {{ request('filter') == 'week' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} rounded-lg transition text-sm">
            <i class="fas fa-calendar-week mr-1"></i>
            Minggu Ini
        </a>
        <a href="{{ route('manajer.reports.index', ['filter' => 'month']) }}"
           class="quick-filter px-4 py-2 {{ request('filter') == 'month' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} rounded-lg transition text-sm">
            <i class="fas fa-calendar-alt mr-1"></i>
            Bulan Ini
        </a>
        <a href="{{ route('manajer.reports.index', ['filter' => 'year']) }}"
           class="quick-filter px-4 py-2 {{ request('filter') == 'year' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} rounded-lg transition text-sm">
            <i class="fas fa-calendar mr-1"></i>
            Tahun Ini
        </a>
    </div>
</div>

<!-- Date & Category Filter -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6 no-print">
    <form action="{{ route('manajer.reports.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-calendar mr-1"></i> Tanggal Mulai
            </label>
            <input type="date" id="start_date" name="start_date" value="{{ $startDate }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
        </div>
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-calendar mr-1"></i> Tanggal Akhir
            </label>
            <input type="date" id="end_date" name="end_date" value="{{ $endDate }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
        </div>
        <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-folder mr-1"></i> Kategori
            </label>
            <select id="category" name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition">
            <i class="fas fa-filter mr-2"></i>
            Terapkan Filter
        </button>
        <a href="{{ route('manajer.reports.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
            <i class="fas fa-redo mr-2"></i>
            Reset
        </a>
    </form>
</div>

<!-- Stock Statistics -->
<div class="mb-6">
    <h4 class="text-md font-semibold text-gray-800 mb-4">
        <i class="fas fa-cubes mr-2"></i>Statistik Stok
    </h4>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Stok</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalStock) }}</p>
                    <p class="text-xs text-gray-500 mt-1">unit</p>
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
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($lowStockCount) }}</p>
                    <p class="text-xs text-gray-500 mt-1">produk</p>
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
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($outOfStockCount) }}</p>
                    <p class="text-xs text-gray-500 mt-1">produk</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <i class="fas fa-times-circle text-red-500 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Statistics -->
<div class="mb-6">
    <h4 class="text-md font-semibold text-gray-800 mb-4">
        <i class="fas fa-exchange-alt mr-2"></i>Statistik Transaksi ({{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})
    </h4>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Transaksi Masuk</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($incomingCount) }}</p>
                    <p class="text-xs text-gray-500 mt-1">transaksi</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-arrow-down text-blue-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Barang Masuk</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalIncoming) }}</p>
                    <p class="text-xs text-gray-500 mt-1">unit</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-box text-purple-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Transaksi Keluar</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($outgoingCount) }}</p>
                    <p class="text-xs text-gray-500 mt-1">transaksi</p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <i class="fas fa-arrow-up text-orange-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-pink-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Barang Keluar</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalOutgoing) }}</p>
                    <p class="text-xs text-gray-500 mt-1">unit</p>
                </div>
                <div class="bg-pink-100 rounded-full p-3">
                    <i class="fas fa-dolly text-pink-500 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Category Distribution -->
<div class="mb-6">
    <h4 class="text-md font-semibold text-gray-800 mb-4">
        <i class="fas fa-chart-pie mr-2"></i>Distribusi Kategori
    </h4>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah Produk</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Stok</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($categoryStats as $stat)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                                    {{ $stat['name'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-gray-900">
                                {{ number_format($stat['product_count']) }} produk
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-gray-900">
                                {{ number_format($stat['total_stock']) }} unit
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h4 class="text-lg font-semibold text-gray-800 mb-4">Transaksi Terbaru</h4>
    @if($recentTransactions->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentTransactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $transaction->product->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $transaction->type === 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $transaction->type === 'in' ? 'Masuk' : 'Keluar' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ $transaction->quantity }} unit
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($transaction->status === 'pending_product_approval') bg-orange-100 text-orange-800
                                    @elseif($transaction->status === 'diterima' || $transaction->status === 'dikeluarkan') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    @if($transaction->status === 'pending_product_approval')
                                        Pending Approval Produk
                                    @else
                                        {{ ucfirst($transaction->status) }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $transaction->user->name ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8">
            <i class="fas fa-history text-gray-300 text-5xl mb-4"></i>
            <p class="text-gray-500">Belum ada transaksi pada periode ini</p>
        </div>
    @endif
</div>

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

        /* Optimize page layout - ensure full width and remove restrictions */
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

        /* Remove margins and padding from grid containers */
        .mb-6, .mb-4 {
            margin-bottom: 0.8rem !important;
        }

        .mt-6, .mt-4 {
            margin-top: 0.5rem !important;
        }

        .p-6, .p-4 {
            padding: 0.8rem !important;
        }

        /* Table styling for print - ENSURE FULL WIDTH */
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

        tfoot {
            display: table-footer-group;
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

        /* Icons in print */
        .fas, .fa {
            font-family: 'Font Awesome 5 Free', FontAwesome !important;
        }

        /* Color adjustments for print - keep some color for better readability */
        .border-green-500 { border-left-color: #10b981 !important; }
        .border-yellow-500 { border-left-color: #f59e0b !important; }
        .border-red-500 { border-left-color: #ef4444 !important; }
        .border-blue-500 { border-left-color: #3b82f6 !important; }
        .border-purple-500 { border-left-color: #8b5cf6 !important; }
        .border-orange-500 { border-left-color: #f97316 !important; }
        .border-pink-500 { border-left-color: #ec4899 !important; }
        .border-teal-500 { border-left-color: #14b8a6 !important; }

        /* Badge styling */
        .bg-green-100,
        .bg-red-100,
        .bg-yellow-100,
        .bg-orange-100,
        .bg-purple-100,
        .bg-blue-100,
        .bg-teal-100 {
            border: 1px solid #333 !important;
            padding: 4px 8px !important;
            background: #f9f9f9 !important;
            color: #000 !important;
        }

        .text-green-800,
        .text-red-800,
        .text-yellow-800,
        .text-orange-800,
        .text-purple-800,
        .text-blue-800,
        .text-teal-800 {
            color: #000 !important;
        }

        /* Page breaks */
        .page-break-after {
            page-break-after: always;
        }

        .page-break-before {
            page-break-before: always;
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

        .text-xl {
            font-size: 1.2rem !important;
        }

        .text-2xl {
            font-size: 1.4rem !important;
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
            max-width: 100% !important;
            height: auto !important;
        }

        /* Remove fixed heights that might cause issues */
        .h-10, .h-full {
            height: auto !important;
        }
    }
</style>

<script>
    // Function to print the report with confirmation
    function printReport() {
        // Confirm before printing
        const confirmed = confirm('Apakah Anda yakin ingin mencetak laporan ini?');

        if (confirmed) {
            // Add a small delay to ensure the page is ready
            setTimeout(function() {
                window.print();
            }, 100);
        }
    }

    // Show notification when print dialog is opened
    window.onbeforeprint = function() {
        console.log('Mempersiapkan cetak laporan...');

        // You can add loading indicator here if needed
        const printButton = document.querySelector('button[onclick="printReport()"]');
        if (printButton) {
            printButton.disabled = true;
            printButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sedang Mencetak...';
        }
    };

    // Reset button after print dialog is closed
    window.onafterprint = function() {
        console.log('Print dialog ditutup');

        // Reset button state
        const printButton = document.querySelector('button[onclick="printReport()"]');
        if (printButton) {
            printButton.disabled = false;
            printButton.innerHTML = '<i class="fas fa-print mr-2"></i>Cetak Laporan';
        }

        // Optional: Show success message
        // alert('Proses cetak selesai!');
    };

    // Prevent accidental page close while printing
    let isPrinting = false;
    window.onbeforeprint = function() {
        isPrinting = true;
    };
    window.onafterprint = function() {
        isPrinting = false;
    };
</script>
@endsection
