@extends('layouts.manajer')

@section('title', 'Riwayat Transaksi')
@section('page-title', 'Riwayat Transaksi')
@section('breadcrumb', 'Home / Riwayat Transaksi')

@section('content')
<!-- Header with Print Button -->
<div class="mb-6 flex justify-between items-center no-print">
    <div>
        <h3 class="text-lg font-semibold text-gray-800">Riwayat Transaksi</h3>
        <p class="text-sm text-gray-600">Semua transaksi barang masuk dan keluar</p>
    </div>
    <button onclick="printTransaction()" id="printBtn" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition shadow-md hover:shadow-lg">
        <i class="fas fa-print mr-2"></i>
        Cetak Riwayat
    </button>
</div>

<!-- Print Header (only visible when printing) -->
<div class="print-only mb-6" style="display: none;">
    <div class="text-center border-b-2 border-gray-800 pb-4 mb-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">STOCKIFY - SISTEM MANAJEMEN STOK</h2>
        <h3 class="text-xl font-semibold text-gray-700 mb-1">RIWAYAT TRANSAKSI</h3>
        <p class="text-sm text-gray-600 mt-2">Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
        <p class="text-sm text-gray-600 font-semibold">Dicetak oleh: {{ auth()->user()->name }} (Manajer)</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-teal-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Transaksi</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalTransactions }}</p>
            </div>
            <div class="bg-teal-100 rounded-full p-3">
                <i class="fas fa-list text-teal-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Barang Masuk</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalIn }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-arrow-down text-green-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Barang Keluar</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalOut }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-arrow-up text-blue-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Pending</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $pending }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-clock text-yellow-500 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-filter text-teal-500 mr-2"></i>
        Filter Riwayat
    </h3>
    <form method="GET" action="{{ route('manajer.transactions.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <!-- Search -->
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
            <input type="text" id="search" name="search" value="{{ request('search') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                placeholder="Produk atau user...">
        </div>

        <!-- Start Date -->
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
        </div>

        <!-- End Date -->
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
        </div>

        <!-- Type Filter -->
        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
            <select id="type" name="type"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                <option value="">Semua Tipe</option>
                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Barang Masuk</option>
                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Barang Keluar</option>
            </select>
        </div>

        <!-- Status Filter -->
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select id="status" name="status"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="pending_product_approval" {{ request('status') == 'pending_product_approval' ? 'selected' : '' }}>Pending Approval Produk</option>
                <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                <option value="dikeluarkan" {{ request('status') == 'dikeluarkan' ? 'selected' : '' }}>Dikeluarkan</option>
                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <!-- Action Buttons (Full Width) -->
        <div class="md:col-span-5 flex gap-2">
            <button type="submit" class="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-search mr-2"></i>Filter
            </button>
            <a href="{{ route('manajer.transactions.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-redo mr-2"></i>Reset
            </a>
        </div>
    </form>
</div>

<!-- Transaction List -->
<div class="bg-white rounded-lg shadow-md">
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-teal-50 to-blue-50">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-history text-teal-500 mr-2"></i>
            Daftar Riwayat Transaksi
        </h3>
        <p class="text-sm text-gray-600 mt-1">Semua transaksi barang masuk dan keluar</p>
    </div>
    <div class="p-6">
        @if($transactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transactions as $transaction)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $transaction->product->name }}</div>
                                    <div class="text-sm text-gray-500">SKU: {{ $transaction->product->sku }}</div>
                                    @if($transaction->product->category)
                                        <span class="mt-1 inline-block px-2 py-0.5 text-xs font-semibold bg-purple-100 text-purple-800 rounded">
                                            {{ $transaction->product->category->name }}
                                        </span>
                                    @endif
                                    <!-- Label: Assignment Type -->
                                    <div class="mt-1">
                                        @if($transaction->assigned_to)
                                            <span class="inline-block px-2 py-0.5 text-xs font-semibold bg-indigo-100 text-indigo-800 rounded">
                                                <i class="fas fa-user-check mr-1"></i>Staff: {{ $transaction->assignedStaff->name ?? '-' }}
                                            </span>
                                        @else
                                            <span class="inline-block px-2 py-0.5 text-xs font-semibold bg-teal-100 text-teal-800 rounded">
                                                <i class="fas fa-hand-paper mr-1"></i>Input Mandiri
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $transaction->type === 'in' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        <i class="fas fa-{{ $transaction->type === 'in' ? 'arrow-down' : 'arrow-up' }} mr-1"></i>
                                        {{ $transaction->type === 'in' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $transaction->quantity }} unit</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $transaction->user->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($transaction->status === 'pending_product_approval') bg-orange-100 text-orange-800
                                        @elseif($transaction->status === 'diterima' || $transaction->status === 'dikeluarkan') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        @if($transaction->status === 'pending')
                                            <i class="fas fa-clock mr-1"></i>
                                        @elseif($transaction->status === 'pending_product_approval')
                                            <i class="fas fa-hourglass-half mr-1"></i>
                                        @elseif($transaction->status === 'diterima' || $transaction->status === 'dikeluarkan')
                                            <i class="fas fa-check-circle mr-1"></i>
                                        @else
                                            <i class="fas fa-times-circle mr-1"></i>
                                        @endif
                                        @if($transaction->status === 'pending_product_approval')
                                            Pending Approval Produk
                                        @else
                                            {{ ucfirst($transaction->status) }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('manajer.transactions.show', $transaction->id) }}"
                                       class="inline-flex items-center px-3 py-1 bg-teal-500 hover:bg-teal-600 text-white font-medium rounded transition-colors duration-200">
                                        <i class="fas fa-eye mr-1"></i>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
                <div class="mt-6">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <i class="fas fa-inbox text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Riwayat</h3>
                <p class="text-gray-500">Belum ada transaksi yang ditemukan dengan filter yang dipilih</p>
            </div>
        @endif
    </div>
</div>

<style>
    @media print {
        /* Hide non-printable elements */
        .no-print,
        nav,
        aside,
        .sidebar,
        button,
        a.inline-flex,
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

        /* Show print header */
        .print-only {
            display: block !important;
        }

        /* Adjust body and main container */
        body {
            margin: 0 !important;
            padding: 10px !important;
            font-size: 11px;
            background: white !important;
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
        .bg-orange-100,
        .bg-teal-100,
        .bg-blue-100,
        .bg-indigo-100,
        .bg-purple-100 {
            border: 1px solid #333 !important;
            padding: 4px 8px !important;
            background: #f9f9f9 !important;
            color: #000 !important;
        }

        .text-green-800,
        .text-red-800,
        .text-yellow-800,
        .text-orange-800,
        .text-teal-800,
        .text-blue-800,
        .text-indigo-800,
        .text-purple-800 {
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

        /* Hide pagination in print */
        .mt-6:has(nav) {
            display: none !important;
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
    // Function to print transaction history with confirmation
    function printTransaction() {
        const confirmed = confirm('Apakah Anda yakin ingin mencetak riwayat transaksi ini?');

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
            btn.innerHTML = '<i class="fas fa-print mr-2"></i>Cetak Riwayat';
        }
    };
</script>
@endsection
