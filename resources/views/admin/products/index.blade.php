@extends('layouts.admin')

@section('title', 'Manajemen Produk')
@section('page-title', 'Manajemen Produk')
@section('breadcrumb', 'Home / Produk')

@section('content')
<!-- Pending Approval Alert -->
@if(isset($pendingCount) && $pendingCount > 0)
<div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded-lg shadow-sm">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-yellow-500 text-2xl mr-3"></i>
            <div>
                <h4 class="text-yellow-800 font-semibold">Ada {{ $pendingCount }} produk menunggu approval</h4>
                <p class="text-yellow-700 text-sm">Produk yang diajukan manajer perlu direview dan disetujui</p>
            </div>
        </div>
        <a href="{{ route('admin.products.approval') }}" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition">
            <i class="fas fa-eye mr-2"></i>
            Lihat Sekarang
        </a>
    </div>
</div>
@endif

<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-lg font-semibold text-gray-800">Daftar Produk</h3>
        <p class="text-sm text-gray-600">Kelola semua produk</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.products.export') }}"
           class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
            <i class="fas fa-file-csv mr-2"></i>
            Export CSV
        </a>
        <a href="{{ route('admin.products.import-form') }}"
           class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
            <i class="fas fa-file-import mr-2"></i>
            Import
        </a>
        <a href="{{ route('admin.products.create') }}"
           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
            <i class="fas fa-plus mr-2"></i>
            Tambah Produk
        </a>
    </div>
</div>

<!-- Stats Card -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Produk</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $products->total() }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-box text-blue-500 text-2xl"></i>
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
    <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-search mr-1"></i> Cari Produk
            </label>
            <input type="text" id="search" name="search" value="{{ request('search') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Cari berdasarkan nama, SKU, atau deskripsi...">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-search mr-2"></i>
                Cari
            </button>
            <a href="{{ route('admin.products.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
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
                                    <img class="h-16 w-16 rounded-lg object-cover" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
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
                                <a href="{{ route('admin.products.show', $product->id) }}" class="inline-flex items-center px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-sm rounded-lg transition" title="Lihat">
                                    <i class="fas fa-eye mr-1"></i>
                                    Lihat
                                </a>
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-lg transition" title="Edit">
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit
                                </a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition" title="Hapus">
                                        <i class="fas fa-trash mr-1"></i>
                                        Hapus
                                    </button>
                                </form>
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
