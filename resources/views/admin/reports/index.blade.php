@extends('layouts.admin')

@section('title', 'Laporan')
@section('page-title', 'Laporan')
@section('breadcrumb', 'Home / Laporan')

@section('content')
<div class="mb-6 flex justify-between items-center no-print">
    <div>
        <h3 class="text-lg font-semibold text-gray-800">Dashboard Laporan</h3>
        <p class="text-sm text-gray-600">Ringkasan dan analisis data sistem inventory</p>
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
        <h3 class="text-xl font-semibold text-gray-700 mb-1">LAPORAN SISTEM INVENTORY</h3>
        <p class="text-sm text-gray-600 mt-2">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        <p class="text-sm text-gray-600">Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
        <p class="text-sm text-gray-600 font-semibold">Dicetak oleh: {{ auth()->user()->name }} (Admin)</p>
    </div>
</div>

<!-- Date Filter -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6 no-print">
    <!-- Quick Filter Buttons -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-bolt mr-1"></i> Filter Cepat
        </label>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.reports.index', ['filter' => 'today']) }}"
               class="px-4 py-2 {{ request('filter') == 'today' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} rounded-lg transition">
                <i class="fas fa-calendar-day mr-1"></i> Hari Ini
            </a>
            <a href="{{ route('admin.reports.index', ['filter' => 'week']) }}"
               class="px-4 py-2 {{ request('filter') == 'week' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} rounded-lg transition">
                <i class="fas fa-calendar-week mr-1"></i> Minggu Ini
            </a>
            <a href="{{ route('admin.reports.index', ['filter' => 'month']) }}"
               class="px-4 py-2 {{ request('filter') == 'month' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} rounded-lg transition">
                <i class="fas fa-calendar-alt mr-1"></i> Bulan Ini
            </a>
            <a href="{{ route('admin.reports.index', ['filter' => 'year']) }}"
               class="px-4 py-2 {{ request('filter') == 'year' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} rounded-lg transition">
                <i class="fas fa-calendar mr-1"></i> Tahun Ini
            </a>
        </div>
    </div>

    <!-- Manual Date Filter -->
    <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-calendar mr-1"></i> Tanggal Mulai
            </label>
            <input type="date" id="start_date" name="start_date" value="{{ $startDate }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-calendar mr-1"></i> Tanggal Akhir
            </label>
            <input type="date" id="end_date" name="end_date" value="{{ $endDate }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
            <i class="fas fa-filter mr-2"></i>
            Terapkan Filter
        </button>
        <a href="{{ route('admin.reports.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
            <i class="fas fa-redo mr-2"></i>
            Reset
        </a>
    </form>
</div>

<!-- General Statistics -->
<div class="mb-6">
    <h4 class="text-md font-semibold text-gray-800 mb-4">
        <i class="fas fa-chart-line mr-2"></i>Statistik Umum
    </h4>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Produk</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalProducts) }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-box text-blue-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Kategori</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalCategories) }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-folder text-purple-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Supplier</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalSuppliers) }}</p>
                </div>
                <div class="bg-indigo-100 rounded-full p-3">
                    <i class="fas fa-truck text-indigo-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-teal-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total User</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalUsers) }}</p>
                </div>
                <div class="bg-teal-100 rounded-full p-3">
                    <i class="fas fa-users text-teal-500 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
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

<!-- Financial Summary -->
<div class="mb-6">
    <h4 class="text-md font-semibold text-gray-800 mb-4">
        <i class="fas fa-dollar-sign mr-2"></i>Ringkasan Finansial Stok
    </h4>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm text-gray-600 mb-2">Nilai Stok (Modal)</div>
            <div class="text-2xl font-bold text-blue-600">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">Harga beli x stok</div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm text-gray-600 mb-2">Potensi Pendapatan</div>
            <div class="text-2xl font-bold text-green-600">Rp {{ number_format($potentialRevenue, 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">Harga jual x stok</div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm text-gray-600 mb-2">Potensi Profit</div>
            <div class="text-2xl font-bold text-emerald-600">Rp {{ number_format($potentialProfit, 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">Jika semua stok terjual</div>
        </div>
    </div>
</div>

<!-- Transaction Statistics -->
<div class="mb-6">
    <h4 class="text-md font-semibold text-gray-800 mb-4">
        <i class="fas fa-exchange-alt mr-2"></i>Statistik Transaksi ({{ date('d M Y', strtotime($startDate)) }} - {{ date('d M Y', strtotime($endDate)) }})
    </h4>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="text-sm text-gray-600 mb-2">Transaksi Masuk</div>
            <div class="text-3xl font-bold text-gray-800">{{ number_format($incomingCount) }}</div>
            <div class="text-xs text-green-600 font-semibold mt-2">{{ number_format($totalIncoming) }} unit</div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
            <div class="text-sm text-gray-600 mb-2">Transaksi Keluar</div>
            <div class="text-3xl font-bold text-gray-800">{{ number_format($outgoingCount) }}</div>
            <div class="text-xs text-red-600 font-semibold mt-2">{{ number_format($totalOutgoing) }} unit</div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="text-sm text-gray-600 mb-2">Pending</div>
            <div class="text-3xl font-bold text-gray-800">{{ number_format($pendingCount) }}</div>
            <div class="text-xs text-gray-500 mt-2">Menunggu approval</div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="text-sm text-gray-600 mb-2">Total Transaksi</div>
            <div class="text-3xl font-bold text-gray-800">{{ number_format($incomingCount + $outgoingCount) }}</div>
            <div class="text-xs text-gray-500 mt-2">Periode ini</div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <div class="text-sm text-gray-600 mb-2">Net Movement</div>
            <div class="text-3xl font-bold {{ ($totalIncoming - $totalOutgoing) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ ($totalIncoming - $totalOutgoing) >= 0 ? '+' : '' }}{{ number_format($totalIncoming - $totalOutgoing) }}
            </div>
            <div class="text-xs text-gray-500 mt-2">unit</div>
        </div>
    </div>
</div>

<!-- Charts Section (Hidden in Print) -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6 no-print">
    <!-- Monthly Trend Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h4 class="text-md font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-line mr-2"></i>Tren Transaksi 6 Bulan Terakhir
        </h4>
        <div style="position: relative; height: 300px;">
            <canvas id="monthlyTrendChart"></canvas>
        </div>
    </div>

    <!-- Category Distribution Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h4 class="text-md font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-pie mr-2"></i>Distribusi Stok per Kategori
        </h4>
        <div style="position: relative; height: 300px;">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
</div>

<!-- Top Products by Value -->
<div class="mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-md font-semibold text-gray-800">
                <i class="fas fa-star mr-2"></i>Top 5 Produk Berdasarkan Nilai Stok
            </h4>
            <a href="{{ route('admin.stock.index') }}" class="text-blue-600 hover:text-blue-700 text-sm">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Produk</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">Stok</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Harga Jual</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Nilai Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($topProductsByValue as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                             class="h-10 w-10 rounded-lg object-cover mr-3">
                                    @else
                                        <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center mr-3">
                                            <i class="fas fa-box text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $product->sku }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm font-semibold text-gray-900">{{ number_format($product->current_stock) }}</span>
                            </td>
                            <td class="px-4 py-3 text-right text-sm text-gray-900">
                                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="text-sm font-bold text-green-600">
                                    Rp {{ number_format($product->current_stock * $product->selling_price, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Low Stock Alert -->
@if($lowStockProducts->count() > 0)
<div class="mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-md font-semibold text-gray-800">
                <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>Produk dengan Stok Rendah/Habis
            </h4>
            <a href="{{ route('admin.stock.index') }}?stock_status=low" class="text-yellow-600 hover:text-yellow-700 text-sm">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Produk</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">Stok Tersedia</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">Minimum</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($lowStockProducts as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                             class="h-10 w-10 rounded-lg object-cover mr-3">
                                    @else
                                        <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center mr-3">
                                            <i class="fas fa-box text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $product->sku }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm font-bold {{ $product->current_stock == 0 ? 'text-red-600' : 'text-yellow-600' }}">
                                    {{ number_format($product->current_stock) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">
                                {{ number_format($product->minimum_stock) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($product->current_stock == 0)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Habis
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Rendah
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.stock-transactions.create') }}?product_id={{ $product->id }}"
                                   class="text-blue-600 hover:text-blue-700 text-sm">
                                    <i class="fas fa-plus-circle mr-1"></i>Tambah Stok
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Recent Transactions -->
<div class="mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-md font-semibold text-gray-800">
                <i class="fas fa-history mr-2"></i>Transaksi Terbaru
            </h4>
            <a href="{{ route('admin.stock-transactions.index') }}" class="text-blue-600 hover:text-blue-700 text-sm">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Produk</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">Tipe</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">Jumlah</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">User</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentTransactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ date('d M Y H:i', strtotime($transaction->date)) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900">{{ $transaction->product->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $transaction->product->sku ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $transaction->type == 'masuk' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-gray-900">
                                {{ number_format($transaction->quantity) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($transaction->status == 'pending')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($transaction->status == 'pending_product_approval')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Pending Approval Produk</span>
                                @elseif($transaction->status == 'diterima')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Diterima</span>
                                @elseif($transaction->status == 'dikeluarkan')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Dikeluarkan</span>
                                @elseif($transaction->status == 'ditolak')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($transaction->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $transaction->user->name ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Export Buttons -->
<div class="flex justify-end gap-3">
    <a href="{{ route('admin.reports.export.stock') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
        <i class="fas fa-file-excel mr-2"></i>
        Export Laporan Stok
    </a>
    <a href="{{ route('admin.reports.export.transactions') }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
        <i class="fas fa-file-excel mr-2"></i>
        Export Laporan Transaksi
    </a>
    <button onclick="exportAllReports()" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
        <i class="fas fa-download mr-2"></i>
        Export Semua Laporan
    </button>
</div>

<script>
function exportAllReports() {
    if (confirm('Export semua laporan (Stok & Transaksi)?')) {
        // Export Stok
        window.open('{{ route('admin.reports.export.stock') }}', '_blank');

        // Delay sedikit sebelum export transaksi
        setTimeout(() => {
            window.open('{{ route('admin.reports.export.transactions') }}?start_date={{ $startDate }}&end_date={{ $endDate }}', '_blank');
        }, 500);
    }
}
</script>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
// Wait for DOM and Chart.js to be fully loaded
window.addEventListener('load', function() {
    console.log('Initializing charts...');

    // Monthly Trend Chart
    try {
        const monthlyCanvas = document.getElementById('monthlyTrendChart');
        if (monthlyCanvas) {
            const monthlyCtx = monthlyCanvas.getContext('2d');

            const monthlyData = {
                labels: {!! json_encode(collect($monthlyTrend)->pluck('month')) !!},
                datasets: [
                    {
                        label: 'Stok Masuk',
                        data: {!! json_encode(collect($monthlyTrend)->pluck('incoming')) !!},
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2
                    },
                    {
                        label: 'Stok Keluar',
                        data: {!! json_encode(collect($monthlyTrend)->pluck('outgoing')) !!},
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2
                    }
                ]
            };

            new Chart(monthlyCtx, {
                type: 'line',
                data: monthlyData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 10
                            }
                        }
                    }
                }
            });
            console.log('Monthly Trend Chart created successfully');
        } else {
            console.error('monthlyTrendChart canvas not found');
        }
    } catch (error) {
        console.error('Error creating Monthly Trend Chart:', error);
    }

    // Category Distribution Chart
    try {
        const categoryCanvas = document.getElementById('categoryChart');
        if (categoryCanvas) {
            const categoryCtx = categoryCanvas.getContext('2d');

            const categoryData = {
                labels: {!! json_encode($categoryStats->pluck('name')) !!},
                datasets: [{
                    data: {!! json_encode($categoryStats->pluck('total_stock')) !!},
                    backgroundColor: [
                        'rgb(59, 130, 246)',
                        'rgb(168, 85, 247)',
                        'rgb(34, 197, 94)',
                        'rgb(251, 146, 60)',
                        'rgb(236, 72, 153)',
                        'rgb(14, 165, 233)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)',
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            };

            new Chart(categoryCtx, {
                type: 'doughnut',
                data: categoryData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.parsed + ' unit';

                                    // Calculate percentage
                                    const dataset = context.dataset;
                                    const total = dataset.data.reduce((acc, val) => acc + val, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    label += ` (${percentage}%)`;

                                    return label;
                                }
                            }
                        }
                    }
                }
            });
            console.log('Category Chart created successfully');
        } else {
            console.error('categoryChart canvas not found');
        }
    } catch (error) {
        console.error('Error creating Category Chart:', error);
    }
});
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
        .sticky,
        canvas,
        #monthlyTrendChart,
        #categoryChart,
        .chart-container {
            display: none !important;
            visibility: hidden !important;
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
        .border-indigo-500 { border-left-color: #6366f1 !important; }

        /* Badge styling */
        .bg-green-100,
        .bg-red-100,
        .bg-yellow-100,
        .bg-purple-100,
        .bg-blue-100,
        .bg-teal-100,
        .bg-orange-100,
        .bg-indigo-100 {
            border: 1px solid #333 !important;
            padding: 4px 8px !important;
            background: #f9f9f9 !important;
            color: #000 !important;
        }

        .text-green-800,
        .text-red-800,
        .text-yellow-800,
        .text-purple-800,
        .text-blue-800,
        .text-teal-800,
        .text-orange-800,
        .text-indigo-800 {
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
        const printButton = document.getElementById('printBtn');
        if (printButton) {
            printButton.disabled = true;
            printButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sedang Mencetak...';
        }
    };

    // Reset button after print dialog is closed
    window.onafterprint = function() {
        console.log('Print dialog ditutup');

        // Reset button state
        const printButton = document.getElementById('printBtn');
        if (printButton) {
            printButton.disabled = false;
            printButton.innerHTML = '<i class="fas fa-print mr-2"></i>Cetak Laporan';
        }
    };
</script>
@endpush
@endsection
