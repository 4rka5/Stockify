@extends('layouts.admin')

@section('title', 'Atribut Produk')
@section('page-title', 'Atribut Produk')
@section('breadcrumb', 'Home / Atribut Produk')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-lg font-semibold text-gray-800">Daftar Atribut Produk</h3>
        <p class="text-sm text-gray-600">Kelola atribut produk seperti ukuran, warna, dll</p>
    </div>
    <a href="{{ route('admin.attributes.create') }}"
       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
        <i class="fas fa-plus mr-2"></i>
        Tambah Atribut
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form method="GET" action="{{ route('admin.attributes.index') }}" class="flex gap-4">
        <div class="flex-1">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari nama atau nilai atribut..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="w-64">
            <select name="product_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Produk</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
            <i class="fas fa-search mr-2"></i>Filter
        </button>
        <a href="{{ route('admin.attributes.index') }}"
           class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
            <i class="fas fa-redo mr-2"></i>Reset
        </a>
    </form>
</div>

<!-- Attributes Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Atribut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($attributes as $attribute)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        #{{ $attribute->id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $attribute->product->name }}</div>
                        <div class="text-xs text-gray-500">SKU: {{ $attribute->product->sku }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $attribute->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $attribute->value }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.attributes.edit', $attribute->id) }}"
                               class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.attributes.destroy', $attribute->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus atribut ini?')"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Tidak ada atribut produk</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($attributes->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $attributes->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
