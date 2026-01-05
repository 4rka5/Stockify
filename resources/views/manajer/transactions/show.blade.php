@extends('layouts.manajer')

@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')
@section('breadcrumb', 'Home / Riwayat Transaksi / Detail')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button and Print Button -->
    <div class="mb-6 flex justify-between items-center no-print">
        <a href="{{ route('manajer.transactions.index') }}" class="inline-flex items-center text-teal-600 hover:text-teal-800">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Riwayat
        </a>
        <button onclick="printDetail()" id="printDetailBtn" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition shadow-md">
            <i class="fas fa-print mr-2"></i>
            Cetak Detail
        </button>
    </div>

    <!-- Print Header (only visible when printing) -->
    <div class="print-only mb-6" style="display: none;">
        <div class="text-center border-b-2 border-gray-800 pb-4 mb-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">STOCKIFY - SISTEM MANAJEMEN STOK</h2>
            <h3 class="text-xl font-semibold text-gray-700 mb-1">DETAIL TRANSAKSI #{{ $transaction->id }}</h3>
            <p class="text-sm text-gray-600 mt-2">Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
            <p class="text-sm text-gray-600 font-semibold">Dicetak oleh: {{ auth()->user()->name }} (Manajer)</p>
        </div>
    </div>

    <!-- Transaction Detail Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-teal-50 to-blue-50">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-file-alt text-teal-500 mr-2"></i>
                Detail Transaksi #{{ $transaction->id }}
            </h3>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ID Transaksi</label>
                        <p class="text-lg font-semibold text-gray-900">#{{ $transaction->id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal</label>
                        <p class="text-lg text-gray-900">
                            <i class="fas fa-calendar-alt text-teal-500 mr-2"></i>
                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('d F Y, H:i') }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Tipe Transaksi</label>
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full
                            {{ $transaction->type === 'in' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            <i class="fas fa-{{ $transaction->type === 'in' ? 'arrow-down' : 'arrow-up' }} mr-2"></i>
                            {{ $transaction->type === 'in' ? 'Barang Masuk' : 'Barang Keluar' }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full
                            @if($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($transaction->status === 'pending_product_approval') bg-orange-100 text-orange-800
                            @elseif($transaction->status === 'diterima' || $transaction->status === 'dikeluarkan') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800
                            @endif">
                            @if($transaction->status === 'pending')
                                <i class="fas fa-clock mr-2"></i>
                            @elseif($transaction->status === 'pending_product_approval')
                                <i class="fas fa-hourglass-half mr-2"></i>
                            @elseif($transaction->status === 'diterima' || $transaction->status === 'dikeluarkan')
                                <i class="fas fa-check-circle mr-2"></i>
                            @else
                                <i class="fas fa-times-circle mr-2"></i>
                            @endif
                            @if($transaction->status === 'pending_product_approval')
                                Pending Approval Produk
                            @else
                                {{ ucfirst($transaction->status) }}
                            @endif
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Dibuat Oleh</label>
                        <p class="text-lg text-gray-900">
                            <i class="fas fa-user text-teal-500 mr-2"></i>
                            {{ $transaction->user->name ?? '-' }}
                        </p>
                    </div>

                    @if($transaction->assignedStaff)
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Ditugaskan Ke</label>
                            <p class="text-lg text-gray-900">
                                <i class="fas fa-user-check text-indigo-500 mr-2"></i>
                                {{ $transaction->assignedStaff->name }}
                            </p>
                        </div>
                    @else
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Jenis Input</label>
                            <span class="inline-block px-2 py-1 text-sm font-semibold bg-teal-100 text-teal-800 rounded">
                                <i class="fas fa-hand-paper mr-1"></i>Input Mandiri Staff
                            </span>
                        </div>
                    @endif

                    @if($transaction->supplier)
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                <i class="fas fa-truck mr-1"></i>
                                {{ $transaction->type === 'in' ? 'Supplier' : 'Tujuan/Customer' }}
                            </label>
                            <p class="text-lg font-bold text-blue-600">
                                {{ $transaction->supplier->name }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-phone mr-1"></i>
                                {{ $transaction->supplier->phone }}
                            </p>
                            @if($transaction->supplier->address)
                                <p class="text-sm text-gray-600 mt-1">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $transaction->supplier->address }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Informasi Produk</label>
                        <div class="space-y-2">
                            <p class="text-lg font-bold text-gray-900">{{ $transaction->product->name }}</p>
                            <p class="text-sm text-gray-600">SKU: {{ $transaction->product->sku }}</p>
                            @if($transaction->product->category)
                                <span class="inline-block px-2 py-1 text-xs font-semibold bg-purple-100 text-purple-800 rounded">
                                    {{ $transaction->product->category->name }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="bg-teal-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Jumlah</label>
                        <p class="text-3xl font-bold text-teal-600">
                            {{ $transaction->quantity }} <span class="text-lg">unit</span>
                        </p>
                    </div>

                    @if($transaction->notes)
                        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                            <label class="block text-sm font-medium text-gray-600 mb-2">
                                <i class="fas fa-sticky-note mr-1"></i>Catatan
                            </label>
                            <p class="text-sm text-gray-700">{{ $transaction->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
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
        a:not(.print-allowed),
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

        .max-w-4xl {
            max-width: none !important;
        }

        /* Remove all scroll containers */
        .overflow-x-auto,
        .overflow-auto,
        .overflow-hidden {
            overflow: visible !important;
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

        /* Badge styling */
        .bg-green-100,
        .bg-red-100,
        .bg-yellow-100,
        .bg-orange-100,
        .bg-teal-100,
        .bg-blue-100,
        .bg-indigo-100 {
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
        .text-indigo-800 {
            color: #000 !important;
        }

        /* Background colors for special sections */
        .bg-gray-50,
        .bg-yellow-50 {
            background: #f9f9f9 !important;
            border: 1px solid #ddd !important;
        }

        /* Page breaks */
        .page-break-after {
            page-break-after: always;
        }

        /* Section headings */
        h3, h4, label {
            page-break-after: avoid;
            font-weight: bold;
        }

        /* Grid layout for print */
        .grid {
            display: grid !important;
            gap: 0.8rem !important;
            width: 100% !important;
        }

        .md\\:grid-cols-2 {
            grid-template-columns: repeat(2, 1fr) !important;
        }

        /* Better text sizes for print */
        .text-lg {
            font-size: 1rem !important;
        }

        .text-xl {
            font-size: 1.1rem !important;
        }

        .text-2xl {
            font-size: 1.3rem !important;
        }

        /* Ensure images don't break layout */
        img {
            max-width: 100% !important;
            height: auto !important;
        }

        /* Remove fixed heights */
        .h-10, .h-full {
            height: auto !important;
        }

        /* Padding adjustments */
        .p-6, .p-4 {
            padding: 0.8rem !important;
        }
    }
</style>

<script>
    // Function to print detail transaction with confirmation
    function printDetail() {
        const confirmed = confirm('Apakah Anda yakin ingin mencetak detail transaksi ini?');

        if (confirmed) {
            setTimeout(function() {
                window.print();
            }, 100);
        }
    }

    // Handle print button state
    window.onbeforeprint = function() {
        const btn = document.getElementById('printDetailBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sedang Mencetak...';
        }
    };

    window.onafterprint = function() {
        const btn = document.getElementById('printDetailBtn');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-print mr-2"></i>Cetak Detail';
        }
    };
</script>
@endsection
