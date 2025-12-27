@extends('layouts.staff')

@section('title', 'Detail Produk')
@section('page-title', 'Detail Produk')
@section('breadcrumb', 'Home / Produk / Detail')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('staff.products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-lg transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar Produk
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Product Image & Basic Info -->
        <div class="lg:col-span-1">
            <!-- Product Image -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="bg-gray-100 rounded-lg h-64 flex items-center justify-center overflow-hidden">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                    @else
                        <i class="fas fa-box text-gray-400 text-6xl"></i>
                    @endif
                </div>
            </div>

            <!-- Stock Status Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-chart-bar text-blue-500 mr-2"></i>
                    Status Stok
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Stok Saat Ini</span>
                        <span class="text-2xl font-bold
                            @if($product->current_stock <= 0) text-red-600
                            @elseif($product->current_stock <= $product->minimum_stock) text-yellow-600
                            @else text-green-600
                            @endif">
                            {{ $product->current_stock }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Minimum Stok</span>
                        <span class="text-xl font-bold text-gray-800">{{ $product->minimum_stock }}</span>
                    </div>
                    <div class="pt-3 border-t border-gray-200">
                        @if($product->current_stock <= 0)
                            <span class="px-3 py-2 bg-red-100 text-red-800 text-sm font-semibold rounded-lg inline-flex items-center w-full justify-center">
                                <i class="fas fa-times-circle mr-2"></i>Stok Habis
                            </span>
                        @elseif($product->current_stock <= $product->minimum_stock)
                            <span class="px-3 py-2 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded-lg inline-flex items-center w-full justify-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Stok Rendah
                            </span>
                        @else
                            <span class="px-3 py-2 bg-green-100 text-green-800 text-sm font-semibold rounded-lg inline-flex items-center w-full justify-center">
                                <i class="fas fa-check-circle mr-2"></i>Stok Aman
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Product Details -->
        <div class="lg:col-span-2">
            <!-- Product Information -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $product->name }}</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-barcode text-blue-500 text-xl mr-3"></i>
                        <div>
                            <p class="text-xs text-gray-600">SKU</p>
                            <p class="font-semibold text-gray-900">{{ $product->sku }}</p>
                        </div>
                    </div>

                    @if($product->category)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-tag text-purple-500 text-xl mr-3"></i>
                        <div>
                            <p class="text-xs text-gray-600">Kategori</p>
                            <p class="font-semibold text-gray-900">{{ $product->category->name }}</p>
                        </div>
                    </div>
                    @endif

                    @if($product->supplier)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-truck text-green-500 text-xl mr-3"></i>
                        <div>
                            <p class="text-xs text-gray-600">Supplier</p>
                            <p class="font-semibold text-gray-900">{{ $product->supplier->name }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-map-marker-alt text-red-500 text-xl mr-3"></i>
                        <div>
                            <p class="text-xs text-gray-600">Lokasi</p>
                            <p class="font-semibold text-gray-900">{{ $product->location ?? 'Tidak ada' }}</p>
                        </div>
                    </div>
                </div>

                @if($product->description)
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Deskripsi</h4>
                    <p class="text-gray-600 leading-relaxed">{{ $product->description }}</p>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Harga Beli</p>
                        <p class="text-2xl font-bold text-green-600">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Harga Jual</p>
                        <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Stock Transaction History -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-history text-purple-500 mr-2"></i>
                    Riwayat Transaksi Stok
                </h3>

                @if($product->stockTransactions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($product->stockTransactions->sortByDesc('created_at')->take(10) as $transaction)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $transaction->type === 'in' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ $transaction->type === 'in' ? 'Masuk' : 'Keluar' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $transaction->quantity }} unit
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($transaction->status === 'diterima' || $transaction->status === 'dikeluarkan') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transaction->user->name ?? 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($product->stockTransactions->count() > 10)
                        <p class="mt-3 text-sm text-gray-500 text-center">Menampilkan 10 transaksi terakhir</p>
                    @endif
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-inbox text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-600">Belum ada riwayat transaksi</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
