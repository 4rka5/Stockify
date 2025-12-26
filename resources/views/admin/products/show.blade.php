@extends('layouts.admin')

@section('title', 'Detail Produk')
@section('page-title', 'Detail Produk')
@section('breadcrumb', 'Home / Produk / Detail')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('admin.products.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Daftar Produk
    </a>
    <div class="flex space-x-2">
        <a href="{{ route('admin.products.edit', $product->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
            <i class="fas fa-edit mr-2"></i>
            Edit Produk
        </a>
        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                <i class="fas fa-trash mr-2"></i>
                Hapus
            </button>
        </form>
    </div>
</div>

<!-- Product Info -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Product Image & Basic Info -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-4">
                @if($product->image)
                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-64 object-cover rounded-lg">
                @else
                    <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-6xl"></i>
                    </div>
                @endif
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $product->name }}</h3>
            <p class="text-sm text-gray-600 mb-4">SKU: {{ $product->sku }}</p>

            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b">
                    <span class="text-gray-600">Kategori:</span>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                        {{ $product->category->name ?? '-' }}
                    </span>
                </div>
                <div class="flex justify-between items-center py-2 border-b">
                    <span class="text-gray-600">Supplier:</span>
                    <span class="font-semibold text-gray-800">{{ $product->supplier->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b">
                    <span class="text-gray-600">Stok Saat Ini:</span>
                    @php
                        $currentStock = $product->current_stock;
                        $isLowStock = $currentStock <= $product->minimum_stock;
                    @endphp
                    <span class="px-3 py-1 text-sm font-semibold rounded-full
                        {{ $isLowStock ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                        {{ $currentStock }} unit
                    </span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-600">Stok Minimum:</span>
                    <span class="font-semibold text-gray-800">{{ $product->minimum_stock }} unit</span>
                </div>
            </div>

            @if($isLowStock)
            <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center text-red-800">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span class="text-sm font-semibold">Peringatan: Stok Rendah!</span>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Details & Stats -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Price Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Informasi Harga</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Harga Beli</p>
                    <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</p>
                </div>
                <div class="p-4 bg-green-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Harga Jual</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                </div>
                <div class="p-4 bg-purple-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Margin</p>
                    @php
                        $margin = $product->selling_price - $product->purchase_price;
                        $marginPercent = $product->purchase_price > 0 ? ($margin / $product->purchase_price) * 100 : 0;
                    @endphp
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($marginPercent, 1) }}%</p>
                    <p class="text-xs text-gray-500">Rp {{ number_format($margin, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Description -->
        @if($product->description)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-3">Deskripsi Produk</h4>
            <p class="text-gray-700">{{ $product->description }}</p>
        </div>
        @endif

        <!-- Stock Statistics -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Statistik Stok</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php
                    $stockIn = $product->stockTransactions->where('type', 'in')->where('status', 'diterima')->sum('quantity');
                    $stockOut = $product->stockTransactions->where('type', 'out')->where('status', 'dikeluarkan')->sum('quantity');
                @endphp
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <i class="fas fa-arrow-down text-green-500 text-2xl mb-2"></i>
                    <p class="text-sm text-gray-600">Total Stok Masuk</p>
                    <p class="text-xl font-bold text-gray-800">{{ $stockIn }} unit</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <i class="fas fa-arrow-up text-red-500 text-2xl mb-2"></i>
                    <p class="text-sm text-gray-600">Total Stok Keluar</p>
                    <p class="text-xl font-bold text-gray-800">{{ $stockOut }} unit</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <i class="fas fa-history text-blue-500 text-2xl mb-2"></i>
                    <p class="text-sm text-gray-600">Total Transaksi</p>
                    <p class="text-xl font-bold text-gray-800">{{ $product->stockTransactions->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h4 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Transaksi Terakhir</h4>
    @if($product->stockTransactions->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($product->stockTransactions->sortByDesc('created_at')->take(10) as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y H:i') }}
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
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $transaction->notes ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($product->stockTransactions->count() > 10)
            <div class="mt-4 text-center">
                <a href="{{ route('admin.stock-transactions.index', ['product_id' => $product->id]) }}" class="text-blue-600 hover:text-blue-800">
                    Lihat semua transaksi <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        @endif
    @else
        <div class="text-center py-8">
            <i class="fas fa-history text-gray-300 text-5xl mb-4"></i>
            <p class="text-gray-500">Belum ada transaksi untuk produk ini</p>
        </div>
    @endif
</div>

<!-- Product Attributes -->
@if($product->attributes->count() > 0)
<div class="bg-white rounded-lg shadow-md p-6 mt-6">
    <h4 class="text-lg font-semibold text-gray-800 mb-4">Atribut Produk</h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($product->attributes as $attribute)
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">{{ $attribute->name }}</p>
                <p class="text-lg font-semibold text-gray-800">{{ $attribute->value }}</p>
            </div>
        @endforeach
    </div>
</div>
@endif

@endsection
