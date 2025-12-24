@extends('layouts.manajer')

@section('title', 'Daftar Supplier')
@section('page-title', 'Daftar Supplier')
@section('breadcrumb', 'Home / Supplier')

@section('content')
<div class="mb-6">
    <div>
        <h3 class="text-lg font-semibold text-gray-800">Daftar Supplier</h3>
        <p class="text-sm text-gray-600">Lihat informasi supplier</p>
    </div>
</div>

<!-- Stats Card -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-teal-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Supplier</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $suppliers->count() }}</p>
            </div>
            <div class="bg-teal-100 rounded-full p-3">
                <i class="fas fa-truck text-teal-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Produk</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $suppliers->sum('products_count') }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-box text-green-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Rata-rata Produk/Supplier</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">
                    {{ $suppliers->count() > 0 ? number_format($suppliers->sum('products_count') / $suppliers->count(), 1) : 0 }}
                </p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-chart-line text-blue-500 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form action="{{ route('manajer.suppliers.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-search mr-1"></i> Cari Supplier
            </label>
            <input type="text" id="search" name="search" value="{{ $keyword ?? '' }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                placeholder="Cari berdasarkan nama, email, telepon, atau alamat...">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition">
                <i class="fas fa-search mr-2"></i>
                Cari
            </button>
            <a href="{{ route('manajer.suppliers.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                <i class="fas fa-redo mr-2"></i>
                Reset
            </a>
        </div>
    </form>

    @if(isset($keyword))
        <div class="mt-3 flex items-center text-sm text-gray-600">
            <i class="fas fa-info-circle mr-2"></i>
            <span>Menampilkan hasil pencarian untuk:
                <span class="font-semibold">"{{ $keyword }}"</span>
                <span class="ml-2">({{ $suppliers->count() }} supplier ditemukan)</span>
            </span>
        </div>
    @endif
</div>

<!-- Suppliers Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Supplier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Produk</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($suppliers as $index => $supplier)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-teal-500 flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($supplier->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $supplier->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                @if($supplier->email)
                                    <div class="flex items-center mb-1">
                                        <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                        {{ $supplier->email }}
                                    </div>
                                @endif
                                @if($supplier->phone)
                                    <div class="flex items-center">
                                        <i class="fas fa-phone text-gray-400 mr-2"></i>
                                        {{ $supplier->phone }}
                                    </div>
                                @endif
                                @if(!$supplier->email && !$supplier->phone)
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-xs truncate">
                                {{ $supplier->address ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $supplier->products_count }} produk
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <i class="fas fa-truck text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">Tidak ada data supplier</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
