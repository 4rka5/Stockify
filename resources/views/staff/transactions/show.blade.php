@extends('layouts.staff')

@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')
@section('breadcrumb', 'Home / Riwayat Transaksi / Detail')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('staff.transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-lg transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Riwayat
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Transaction Status Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Status Transaksi
                </h3>

                <div class="space-y-4">
                    <!-- Transaction Type -->
                    <div class="text-center p-4 rounded-lg
                        {{ $transaction->type === 'in' ? 'bg-green-50 border border-green-200' : 'bg-blue-50 border border-blue-200' }}">
                        <i class="fas fa-{{ $transaction->type === 'in' ? 'arrow-down' : 'arrow-up' }}
                            {{ $transaction->type === 'in' ? 'text-green-500' : 'text-blue-500' }} text-4xl mb-2"></i>
                        <p class="text-lg font-bold {{ $transaction->type === 'in' ? 'text-green-800' : 'text-blue-800' }}">
                            {{ $transaction->type === 'in' ? 'BARANG MASUK' : 'BARANG KELUAR' }}
                        </p>
                    </div>

                    <!-- Status Badge -->
                    <div class="text-center p-4 rounded-lg
                        @if($transaction->status === 'pending') bg-yellow-50 border border-yellow-200
                        @elseif($transaction->status === 'diterima' || $transaction->status === 'dikeluarkan') bg-green-50 border border-green-200
                        @else bg-red-50 border border-red-200
                        @endif">
                        <p class="text-sm text-gray-600 mb-2">Status</p>
                        <span class="inline-flex items-center px-4 py-2 text-lg font-bold rounded-full
                            @if($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($transaction->status === 'diterima' || $transaction->status === 'dikeluarkan') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800
                            @endif">
                            @if($transaction->status === 'pending')
                                <i class="fas fa-clock mr-2"></i>
                            @elseif($transaction->status === 'diterima' || $transaction->status === 'dikeluarkan')
                                <i class="fas fa-check-circle mr-2"></i>
                            @else
                                <i class="fas fa-times-circle mr-2"></i>
                            @endif
                            {{ strtoupper($transaction->status) }}
                        </span>
                    </div>

                    <!-- Quantity -->
                    <div class="text-center p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <p class="text-sm text-gray-600 mb-2">Jumlah</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $transaction->quantity }}</p>
                        <p class="text-sm text-gray-600">unit</p>
                    </div>

                    <!-- Date -->
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center text-sm text-gray-600 mb-1">
                            <i class="fas fa-calendar mr-2"></i>
                            <span class="font-medium">Tanggal Transaksi:</span>
                        </div>
                        <p class="text-gray-900 font-semibold ml-6">
                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('d F Y, H:i') }}
                        </p>
                    </div>

                    <!-- User -->
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center text-sm text-gray-600 mb-1">
                            <i class="fas fa-user mr-2"></i>
                            <span class="font-medium">Dibuat Oleh:</span>
                        </div>
                        <p class="text-gray-900 font-semibold ml-6">{{ $transaction->user->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product & Transaction Details -->
        <div class="lg:col-span-2">
            <!-- Product Information -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-box text-purple-500 mr-2"></i>
                    Informasi Produk
                </h3>

                <div class="flex items-start gap-4">
                    <!-- Product Image -->
                    <div class="flex-shrink-0 bg-gray-100 rounded-lg h-32 w-32 flex items-center justify-center overflow-hidden">
                        @if($transaction->product->image)
                            <img src="{{ asset('storage/' . $transaction->product->image) }}"
                                 alt="{{ $transaction->product->name }}"
                                 class="h-full w-full object-cover">
                        @else
                            <i class="fas fa-box text-gray-400 text-4xl"></i>
                        @endif
                    </div>

                    <!-- Product Details -->
                    <div class="flex-1">
                        <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $transaction->product->name }}</h4>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex items-center text-sm">
                                <i class="fas fa-barcode text-gray-400 w-5 mr-2"></i>
                                <div>
                                    <p class="text-xs text-gray-600">SKU</p>
                                    <p class="font-semibold text-gray-900">{{ $transaction->product->sku }}</p>
                                </div>
                            </div>

                            @if($transaction->product->category)
                            <div class="flex items-center text-sm">
                                <i class="fas fa-tag text-gray-400 w-5 mr-2"></i>
                                <div>
                                    <p class="text-xs text-gray-600">Kategori</p>
                                    <p class="font-semibold text-gray-900">{{ $transaction->product->category->name }}</p>
                                </div>
                            </div>
                            @endif

                            @if($transaction->product->supplier)
                            <div class="flex items-center text-sm">
                                <i class="fas fa-truck text-gray-400 w-5 mr-2"></i>
                                <div>
                                    <p class="text-xs text-gray-600">Supplier</p>
                                    <p class="font-semibold text-gray-900">{{ $transaction->product->supplier->name }}</p>
                                </div>
                            </div>
                            @endif

                            <div class="flex items-center text-sm">
                                <i class="fas fa-warehouse text-gray-400 w-5 mr-2"></i>
                                <div>
                                    <p class="text-xs text-gray-600">Stok Saat Ini</p>
                                    <p class="font-bold {{ $transaction->product->current_stock > $transaction->product->minimum_stock ? 'text-green-600' : 'text-yellow-600' }}">
                                        {{ $transaction->product->current_stock }} unit
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Details -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-file-alt text-green-500 mr-2"></i>
                    Detail Transaksi
                </h3>

                <div class="space-y-3">
                    @if($transaction->notes)
                    <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-sticky-note text-yellow-500 mt-1 mr-3"></i>
                        <div class="flex-1">
                            <p class="text-sm text-gray-600 mb-1">Catatan</p>
                            <p class="text-gray-900">{{ $transaction->notes }}</p>
                        </div>
                    </div>
                    @endif

                    @if($transaction->status === 'ditolak')
                    <div class="flex items-start p-4 bg-red-50 border border-red-200 rounded-lg">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mt-1 mr-3"></i>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-red-800 mb-1">Transaksi Ditolak</p>
                            <p class="text-sm text-red-700">{{ $transaction->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Timeline (if approved/rejected) -->
            @if($transaction->status !== 'pending')
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-clock text-orange-500 mr-2"></i>
                    Timeline
                </h3>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-plus text-blue-500"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="font-semibold text-gray-900">Transaksi Dibuat</p>
                            <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d F Y, H:i') }}</p>
                            <p class="text-sm text-gray-500">Oleh: {{ $transaction->user->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-10 h-10 {{ $transaction->status === 'ditolak' ? 'bg-red-100' : 'bg-green-100' }} rounded-full flex items-center justify-center">
                            <i class="fas fa-{{ $transaction->status === 'ditolak' ? 'times' : 'check' }} {{ $transaction->status === 'ditolak' ? 'text-red-500' : 'text-green-500' }}"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="font-semibold text-gray-900">
                                {{ $transaction->status === 'ditolak' ? 'Transaksi Ditolak' : 'Transaksi Disetujui' }}
                            </p>
                            <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($transaction->updated_at)->format('d F Y, H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
