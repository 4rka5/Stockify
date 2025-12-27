@extends('layouts.staff')

@section('title', 'Cek Stok')
@section('page-title', 'Cek Stok Barang')
@section('breadcrumb', 'Home / Stok / Cek Stok')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Produk</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalProducts }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-boxes text-blue-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Stok Aman</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $safeStock }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-check-circle text-green-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Stok Rendah</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $lowStock }}</p>
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
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $outOfStock }}</p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-times-circle text-red-500 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-filter text-blue-500 mr-2"></i>
        Filter Pencarian
    </h3>
    <form method="GET" action="{{ route('staff.stock.check') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Search -->
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari Produk</label>
            <input type="text" id="search" name="search" value="{{ request('search') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Nama atau SKU...">
        </div>

        <!-- Category Filter -->
        <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
            <select id="category" name="category"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Stock Status Filter -->
        <div>
            <label for="stock_status" class="block text-sm font-medium text-gray-700 mb-2">Status Stok</label>
            <select id="stock_status" name="stock_status"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Status</option>
                <option value="safe" {{ request('stock_status') == 'safe' ? 'selected' : '' }}>Stok Aman</option>
                <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stok Rendah</option>
                <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Stok Habis</option>
            </select>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-search mr-2"></i>Filter
            </button>
            <a href="{{ route('staff.stock.check') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-redo"></i>
            </a>
        </div>
    </form>
</div>

<!-- Product List -->
<div class="bg-white rounded-lg shadow-md">
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-warehouse text-blue-500 mr-2"></i>
                    Daftar Stok Barang
                </h3>
                <p class="text-sm text-gray-600 mt-1">Informasi stok barang yang tersedia di gudang</p>
            </div>
        </div>
    </div>
    <div class="p-6">
        @if($products->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Produk
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kategori
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stok Tersedia
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Min. Stok
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lokasi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($products as $product)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                            <div class="text-sm text-gray-500">SKU: {{ $product->sku }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($product->category)
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            {{ $product->category->name }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold
                                        @if($product->current_stock <= 0) text-red-600
                                        @elseif($product->current_stock <= $product->minimum_stock) text-yellow-600
                                        @else text-green-600
                                        @endif">
                                        {{ $product->current_stock }} unit
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $product->minimum_stock }} unit
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($product->current_stock <= 0)
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Habis
                                        </span>
                                    @elseif($product->current_stock <= $product->minimum_stock)
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Rendah
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Aman
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $product->location ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="mt-6">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <i class="fas fa-search text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Data</h3>
                <p class="text-gray-500">Tidak ada produk yang ditemukan dengan filter yang dipilih</p>
            </div>
        @endif
    </div>
</div>

<!-- Legend -->
<div class="bg-white rounded-lg shadow-md p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
        Keterangan Status
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="flex items-center p-3 bg-green-50 rounded-lg border border-green-200">
            <div class="bg-green-100 rounded-full p-2 mr-3">
                <i class="fas fa-check-circle text-green-500"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-800">Stok Aman</p>
                <p class="text-xs text-gray-600">Stok di atas minimum</p>
            </div>
        </div>
        <div class="flex items-center p-3 bg-yellow-50 rounded-lg border border-yellow-200">
            <div class="bg-yellow-100 rounded-full p-2 mr-3">
                <i class="fas fa-exclamation-triangle text-yellow-500"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-800">Stok Rendah</p>
                <p class="text-xs text-gray-600">Stok mencapai batas minimum</p>
            </div>
        </div>
        <div class="flex items-center p-3 bg-red-50 rounded-lg border border-red-200">
            <div class="bg-red-100 rounded-full p-2 mr-3">
                <i class="fas fa-times-circle text-red-500"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-800">Stok Habis</p>
                <p class="text-xs text-gray-600">Stok habis, perlu restock</p>
            </div>
        </div>
    </div>
</div>
@endsection
