@extends('layouts.staff')

@section('title', 'Daftar Produk')
@section('page-title', 'Daftar Produk')
@section('breadcrumb', 'Home / Produk')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
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
    <form method="GET" action="{{ route('staff.products.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
            <a href="{{ route('staff.products.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-redo"></i>
            </a>
        </div>
    </form>
</div>

<!-- Product List -->
<div class="bg-white rounded-lg shadow-md">
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-box-open text-blue-500 mr-2"></i>
            Daftar Produk
        </h3>
        <p class="text-sm text-gray-600 mt-1">Informasi produk yang tersedia</p>
    </div>
    <div class="p-6">
        @if($products->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                    <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-200">
                        <!-- Product Image -->
                        <div class="bg-gray-100 h-50 flex items-center justify-center">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                            @else
                                <i class="fas fa-box text-gray-400 text-6x2"></i>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <!-- Category Badge -->
                            @if($product->category)
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded">
                                    {{ $product->category->name }}
                                </span>
                            @endif

                            <!-- Product Name -->
                            <h4 class="mt-2 text-lg font-bold text-gray-900 line-clamp-2">{{ $product->name }}</h4>

                            <!-- SKU -->
                            <p class="text-sm text-gray-500 mt-1">SKU: {{ $product->sku }}</p>

                            <!-- Stock Info -->
                            <div class="mt-3 flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-600">Stok Tersedia</p>
                                    <p class="text-lg font-bold
                                        @if($product->current_stock <= 0) text-red-600
                                        @elseif($product->current_stock <= $product->minimum_stock) text-yellow-600
                                        @else text-green-600
                                        @endif">
                                        {{ $product->current_stock }} unit
                                    </p>
                                </div>
                                <div>
                                    @if($product->current_stock <= 0)
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded">
                                            Habis
                                        </span>
                                    @elseif($product->current_stock <= $product->minimum_stock)
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded">
                                            Rendah
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded">
                                            Aman
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Price Info -->
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Harga Beli:</span>
                                    <span class="font-semibold text-gray-800">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-sm mt-1">
                                    <span class="text-gray-600">Harga Jual:</span>
                                    <span class="font-semibold text-blue-600">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <a href="{{ route('staff.products.show', $product->id) }}"
                               class="mt-4 block w-full px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-center font-medium rounded-lg transition-colors duration-200">
                                <i class="fas fa-eye mr-2"></i>Lihat Detail
                            </a>
                        </div>
                    </div>
                @endforeach
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
@endsection
