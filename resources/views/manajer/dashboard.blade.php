@extends('layouts.manajer')

@section('title', 'Manajer Dashboard')
@section('page-title', 'Dashboard Manajer')
@section('breadcrumb', 'Home / Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Stok Menipis Card -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Stok Menipis</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $lowStockProducts->count() }}</p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Barang Masuk Hari Ini -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Barang Masuk Hari Ini</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $todayIncoming->count() }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-arrow-down text-green-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Barang Keluar Hari Ini -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Barang Keluar Hari Ini</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $todayOutgoing->count() }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-arrow-up text-blue-500 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Pending Approval -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-clock text-yellow-500 mr-2"></i>
                Menunggu Persetujuan
            </h3>
        </div>
        <div class="p-6">
            @if($pendingTransactions->count() > 0)
                <div class="space-y-3">
                    @foreach($pendingTransactions->take(5) as $transaction)
                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-800">{{ $transaction->product->name }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $transaction->user->name }} • {{ $transaction->date->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <form action="{{ route('manajer.stock.approve', $transaction->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold rounded">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('manajer.stock.reject', $transaction->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600 text-center py-4">Tidak ada transaksi yang menunggu persetujuan</p>
            @endif
        </div>
    </div>

    <!-- Low Stock Products -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                Produk Stok Menipis
            </h3>
        </div>
        <div class="p-6">
            @if($lowStockProducts->count() > 0)
                <div class="space-y-3">
                    @foreach($lowStockProducts->take(5) as $product)
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-800">{{ $product->name }}</p>
                                <p class="text-sm text-gray-600">Minimal: {{ $product->minimum_stock }} unit</p>
                            </div>
                            <span class="px-3 py-1 bg-red-500 text-white text-sm font-semibold rounded-full">
                                {{ $product->current_stock }} unit
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600 text-center py-4">Semua stok produk dalam kondisi aman</p>
            @endif
        </div>
    </div>
</div>

<!-- Transaksi Barang Masuk dan Keluar Hari Ini -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <!-- Barang Masuk Hari Ini -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-arrow-down text-green-500 mr-2"></i>
                Detail Barang Masuk Hari Ini
            </h3>
        </div>
        <div class="p-6">
            @if($todayIncoming->count() > 0)
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($todayIncoming as $transaction)
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">{{ $transaction->product->name }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $transaction->user->name }} • {{ $transaction->date->format('H:i') }}
                                </p>
                                @if($transaction->notes)
                                    <p class="text-xs text-gray-500 mt-1">{{ $transaction->notes }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 bg-green-500 text-white text-sm font-semibold rounded-full">
                                    +{{ $transaction->quantity }} unit
                                </span>
                                <p class="text-xs text-gray-600 mt-1">
                                    @if($transaction->status === 'diterima')
                                        <span class="text-green-600"><i class="fas fa-check-circle"></i> Diterima</span>
                                    @elseif($transaction->status === 'pending')
                                        <span class="text-yellow-600"><i class="fas fa-clock"></i> Pending</span>
                                    @else
                                        <span class="text-red-600"><i class="fas fa-times-circle"></i> Ditolak</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Total Barang Masuk:</span>
                        <span class="text-lg font-bold text-green-600">
                            {{ $todayIncoming->where('status', 'diterima')->sum('quantity') }} unit
                        </span>
                    </div>
                </div>
            @else
                <p class="text-gray-600 text-center py-4">Belum ada barang masuk hari ini</p>
            @endif
        </div>
    </div>

    <!-- Barang Keluar Hari Ini -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-arrow-up text-blue-500 mr-2"></i>
                Detail Barang Keluar Hari Ini
            </h3>
        </div>
        <div class="p-6">
            @if($todayOutgoing->count() > 0)
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($todayOutgoing as $transaction)
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">{{ $transaction->product->name }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $transaction->user->name }} • {{ $transaction->date->format('H:i') }}
                                </p>
                                @if($transaction->notes)
                                    <p class="text-xs text-gray-500 mt-1">{{ $transaction->notes }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 bg-blue-500 text-white text-sm font-semibold rounded-full">
                                    -{{ $transaction->quantity }} unit
                                </span>
                                <p class="text-xs text-gray-600 mt-1">
                                    @if($transaction->status === 'dikeluarkan')
                                        <span class="text-green-600"><i class="fas fa-check-circle"></i> Dikeluarkan</span>
                                    @elseif($transaction->status === 'pending')
                                        <span class="text-yellow-600"><i class="fas fa-clock"></i> Pending</span>
                                    @else
                                        <span class="text-red-600"><i class="fas fa-times-circle"></i> Ditolak</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Total Barang Keluar:</span>
                        <span class="text-lg font-bold text-blue-600">
                            {{ $todayOutgoing->where('status', 'dikeluarkan')->sum('quantity') }} unit
                        </span>
                    </div>
                </div>
            @else
                <p class="text-gray-600 text-center py-4">Belum ada barang keluar hari ini</p>
            @endif
        </div>
    </div>
</div>
@endsection
