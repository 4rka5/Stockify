@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard Admin')
@section('breadcrumb', 'Home / Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Products Card -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Produk</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalProducts }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-box text-blue-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Users Card -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total User</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalUsers }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-users text-green-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Categories Card -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Kategori</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalCategories }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-list text-purple-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Pending Transactions Card -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Transaksi Pending</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $pendingTransactions->count() }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-clock text-yellow-500 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Total Transactions In Card -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-emerald-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Transaksi Masuk (Bulan Ini)</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalTransactionsIn }}</p>
                <p class="text-sm text-gray-500 mt-1">Total barang masuk gudang</p>
            </div>
            <div class="bg-emerald-100 rounded-full p-3">
                <i class="fas fa-arrow-down text-emerald-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Transactions Out Card -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-rose-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Transaksi Keluar (Bulan Ini)</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalTransactionsOut }}</p>
                <p class="text-sm text-gray-500 mt-1">Total barang keluar gudang</p>
            </div>
            <div class="bg-rose-100 rounded-full p-3">
                <i class="fas fa-arrow-up text-rose-500 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Top Stock Products Chart -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-chart-bar text-indigo-500 mr-2"></i>
                Top 5 Produk Berdasarkan Stok
            </h3>
        </div>
        <div class="p-6">
            @if($topStockProducts->count() > 0)
                <div class="space-y-4">
                    @foreach($topStockProducts as $index => $product)
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ $product->name }}</span>
                                <span class="text-sm font-bold text-gray-900">{{ number_format($product->current_stock) }} unit</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                @php
                                    $maxStock = $topStockProducts->first()->current_stock;
                                    $percentage = $maxStock > 0 ? ($product->current_stock / $maxStock) * 100 : 0;
                                    $colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-yellow-500', 'bg-pink-500'];
                                    $colorIndex = $index % count($colors); // Pastikan index selalu dalam range
                                @endphp
                                <div class="{{ $colors[$colorIndex] }} h-3 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600 text-center py-4">Belum ada data produk</p>
            @endif
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-history text-orange-500 mr-2"></i>
                Aktivitas Terbaru
            </h3>
        </div>
        <div class="p-6">
            @if($recentActivities->count() > 0)
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($recentActivities as $activity)
                        <div class="flex items-start space-x-3 p-3 {{ $activity->type === 'in' ? 'bg-green-50' : 'bg-blue-50' }} rounded-lg">
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-8 h-8 rounded-full {{ $activity->type === 'in' ? 'bg-green-500' : 'bg-blue-500' }} flex items-center justify-center">
                                    <i class="fas {{ $activity->type === 'in' ? 'fa-arrow-down' : 'fa-arrow-up' }} text-white text-xs"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $activity->type === 'in' ? 'Barang Masuk' : 'Barang Keluar' }}
                                </p>
                                <p class="text-sm text-gray-700">{{ $activity->product->name }}</p>
                                <p class="text-xs text-gray-600 mt-1">
                                    {{ $activity->quantity }} unit • oleh {{ $activity->user->name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $activity->date->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600 text-center py-4">Belum ada aktivitas</p>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Low Stock Products -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                Stok Menipis
            </h3>
        </div>
        <div class="p-6">
            @if($lowStockProducts->count() > 0)
                <div class="space-y-3">
                    @foreach($lowStockProducts->take(5) as $product)
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-800">{{ $product->name }}</p>
                                <p class="text-sm text-gray-600">SKU: {{ $product->sku }}</p>
                            </div>
                            <span class="px-3 py-1 bg-red-500 text-white text-sm font-semibold rounded-full">
                                {{ $product->current_stock }} unit
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600 text-center py-4">Tidak ada produk dengan stok menipis</p>
            @endif
        </div>
    </div>

    <!-- Today's Transactions -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-exchange-alt text-blue-500 mr-2"></i>
                Transaksi Hari Ini
            </h3>
        </div>
        <div class="p-6">
            @if($todayTransactions->count() > 0)
                <div class="space-y-3">
                    @foreach($todayTransactions->take(5) as $transaction)
                        <div class="flex items-center justify-between p-3 {{ $transaction->type === 'in' ? 'bg-green-50' : 'bg-blue-50' }} rounded-lg">
                            <div>
                                <p class="font-medium text-gray-800">{{ $transaction->product->name }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $transaction->user->name }} • {{ $transaction->date->format('H:i') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 {{ $transaction->type === 'in' ? 'bg-green-500' : 'bg-blue-500' }} text-white text-sm font-semibold rounded-full">
                                    {{ $transaction->type === 'in' ? '+' : '-' }}{{ $transaction->quantity }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600 text-center py-4">Belum ada transaksi hari ini</p>
            @endif
        </div>
    </div>
</div>
@endsection
