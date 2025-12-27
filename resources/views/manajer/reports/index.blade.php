@extends('layouts.manajer')

@section('title', 'Laporan Stok')
@section('page-title', 'Laporan Stok')
@section('breadcrumb', 'Home / Laporan')

@section('content')
<div class="mb-6">
    <h3 class="text-lg font-semibold text-gray-800">Laporan Stok Barang</h3>
    <p class="text-sm text-gray-600">Ringkasan dan analisis data stok</p>
</div>

<!-- Quick Filter Buttons -->
<div class="bg-white rounded-lg shadow-md p-4 mb-4">
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
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
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
                                    {{ $transaction->status === 'diterima' || $transaction->status === 'dikeluarkan' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($transaction->status) }}
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
@endsection
