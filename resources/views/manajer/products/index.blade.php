@extends('layouts.manajer')

@section('title', 'Daftar Produk')
@section('page-title', 'Daftar Produk')
@section('breadcrumb', 'Home / Produk')

@section('content')
<div class="mb-6 flex justify-between items-start">
    <div>
        <h3 class="text-lg font-semibold text-gray-800">Daftar Produk</h3>
        <p class="text-sm text-gray-600">Lihat informasi semua produk</p>
    </div>
    <a href="{{ route('manajer.products.create') }}" class="inline-flex items-center px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
        <i class="fas fa-plus-circle mr-2"></i>
        Ajukan Produk Baru
    </a>
</div>

<!-- Info Alert -->
<div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-500 text-lg"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-blue-700">
                <strong>Catatan:</strong> Untuk menambah produk baru, klik tombol "Ajukan Produk Baru". Produk akan muncul di daftar setelah admin menyetujui pengajuan Anda.
            </p>
        </div>
    </div>
</div>

<!-- Stats Card -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-teal-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Produk</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $products->total() }}</p>
            </div>
            <div class="bg-teal-100 rounded-full p-3">
                <i class="fas fa-box text-teal-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Produk Aktif</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $products->total() }}</p>
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
                <p class="text-3xl font-bold text-gray-800 mt-2">
                    {{ $products->filter(function($p) { return $p->current_stock <= $p->minimum_stock; })->count() }}
                </p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Kategori</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">
                    {{ $products->pluck('category_id')->unique()->count() }}
                </p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-folder text-purple-500 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-md font-semibold text-gray-800">
            <i class="fas fa-filter text-teal-500 mr-2"></i>
            Pencarian Produk
        </h3>
        {{-- <a href="{{ route('manajer.products.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-teal-500 to-blue-500 hover:from-teal-600 hover:to-blue-600 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow hover:shadow-lg">
            <i class="fas fa-plus mr-2"></i>
            Tambah Produk
        </a> --}}
    </div>
    <form action="{{ route('manajer.products.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-search mr-1"></i> Cari Produk
            </label>
            <input type="text" id="search" name="search" value="{{ request('search') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                placeholder="Cari berdasarkan nama, SKU, atau deskripsi...">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition">
                <i class="fas fa-search mr-2"></i>
                Cari
            </button>
            <a href="{{ route('manajer.products.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                <i class="fas fa-redo mr-2"></i>
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Products Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex-shrink-0 h-16 w-16">
                                @if($product->image)
                                    <img class="h-16 w-16 rounded-lg object-cover" src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}">
                                @else
                                    <div class="h-16 w-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 text-2xl"></i>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                            <div class="text-sm text-gray-500">SKU: {{ $product->sku }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ $product->category->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $product->supplier->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div>Beli: Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</div>
                                <div class="text-green-600 font-semibold">Jual: Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @php
                                $currentStock = $product->current_stock;
                                $isLowStock = $currentStock <= $product->minimum_stock;
                            @endphp
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                                {{ $isLowStock ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ $currentStock }} unit
                            </span>
                            @if($isLowStock)
                                <div class="text-xs text-red-600 mt-1">
                                    <i class="fas fa-exclamation-circle"></i> Stok Rendah
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('manajer.products.show', $product->id) }}" class="inline-flex items-center px-3 py-2 bg-teal-500 hover:bg-teal-600 text-white text-sm rounded-lg transition" title="Lihat Detail">
                                    <i class="fas fa-eye mr-1"></i>
                                    Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <i class="fas fa-box-open text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">Tidak ada data produk</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 bg-gray-50">
        {{ $products->links() }}
    </div>
</div>
@endsection
