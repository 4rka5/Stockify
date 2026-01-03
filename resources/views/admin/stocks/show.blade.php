@extends('layouts.admin')

@section('title', 'Detail Transaksi Stok')
@section('page-title', 'Detail Transaksi Stok')
@section('breadcrumb', 'Home / Transaksi Stok / Detail')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('admin.stock-transactions.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Daftar Transaksi
    </a>
    <div class="flex space-x-2">
        <a href="{{ route('admin.stock-transactions.edit', $transaction->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
            <i class="fas fa-edit mr-2"></i>
            Edit
        </a>
        <form action="{{ route('admin.stock-transactions.destroy', $transaction->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                <i class="fas fa-trash mr-2"></i>
                Hapus
            </button>
        </form>
    </div>
</div>

<!-- Transaction Header -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">
                Transaksi #{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}
            </h3>
            <p class="text-sm text-gray-600 mt-1">
                {{ \Carbon\Carbon::parse($transaction->date)->format('d F Y, H:i') }} WIB
            </p>
        </div>
        <div class="text-right">
            @php
                $typeColors = [
                    'in' => 'bg-green-100 text-green-800',
                    'out' => 'bg-red-100 text-red-800',
                ];
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'diterima' => 'bg-green-100 text-green-800',
                    'dikeluarkan' => 'bg-blue-100 text-blue-800',
                    'ditolak' => 'bg-red-100 text-red-800',
                ];
            @endphp
            <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full {{ $typeColors[$transaction->type] ?? 'bg-gray-100 text-gray-800' }} mb-2">
                <i class="fas fa-arrow-{{ $transaction->type === 'in' ? 'down' : 'up' }} mr-1"></i>
                {{ $transaction->type === 'in' ? 'Stok Masuk' : 'Stok Keluar' }}
            </span>
            <br>
            <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full {{ $statusColors[$transaction->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ ucfirst($transaction->status) }}
            </span>
        </div>
    </div>
</div>

<!-- Transaction Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Product Info -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Informasi Produk</h4>
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    @if($transaction->product->image)
                        <img src="{{ asset('storage/' . $transaction->product->image) }}" alt="{{ $transaction->product->name }}" class="w-32 h-32 object-cover rounded-lg">
                    @else
                        <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-box text-gray-400 text-4xl"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <h5 class="text-xl font-semibold text-gray-800 mb-2">{{ $transaction->product->name }}</h5>
                    <div class="space-y-2">
                        <div class="flex items-center text-sm">
                            <span class="text-gray-600 w-32">SKU:</span>
                            <span class="font-semibold text-gray-800">{{ $transaction->product->sku }}</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-gray-600 w-32">Kategori:</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ $transaction->product->category->name ?? '-' }}
                            </span>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-gray-600 w-32">Supplier:</span>
                            <span class="font-semibold text-gray-800">{{ $transaction->product->supplier->name ?? '-' }}</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-gray-600 w-32">Stok Saat Ini:</span>
                            <span class="font-semibold text-gray-800">{{ $transaction->product->current_stock }} unit</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Detail Transaksi</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Jumlah</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $transaction->quantity }} <span class="text-lg">unit</span></p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Tanggal Transaksi</p>
                    <p class="text-lg font-bold text-gray-800">{{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}</p>
                    <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($transaction->date)->format('H:i') }} WIB</p>
                </div>
            </div>

            @if($transaction->notes)
            <div class="mt-6">
                <h5 class="text-sm font-semibold text-gray-700 mb-2">Catatan:</h5>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-700">{{ $transaction->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Side Info -->
    <div class="space-y-6">
        <!-- User Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pengguna</h4>
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 h-12 w-12">
                    <div class="h-12 w-12 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr($transaction->user->name ?? 'U', 0, 1)) }}
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ $transaction->user->name ?? 'Unknown' }}</div>
                    <div class="text-sm text-gray-500">{{ $transaction->user->email ?? '-' }}</div>
                </div>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Role:</span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ ucfirst($transaction->user->role ?? '-') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        @if($transaction->status === 'pending')
        <div class="bg-white rounded-lg shadow-md p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Aksi</h4>
            <div class="space-y-3">
                <form action="{{ route('admin.stock-transactions.approve', $transaction->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                        <i class="fas fa-check mr-2"></i>
                        Setujui Transaksi
                    </button>
                </form>
                <form action="{{ route('admin.stock-transactions.reject', $transaction->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menolak transaksi ini?')">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                        <i class="fas fa-times mr-2"></i>
                        Tolak Transaksi
                    </button>
                </form>
            </div>
        </div>
        @endif

        <!-- Timeline -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Timeline</h4>
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                            <i class="fas fa-plus text-white text-xs"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Transaksi Dibuat</p>
                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($transaction->date)->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                @if($transaction->status !== 'pending')
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-full {{ $transaction->status === 'ditolak' ? 'bg-red-500' : 'bg-green-500' }} flex items-center justify-center">
                            <i class="fas fa-{{ $transaction->status === 'ditolak' ? 'times' : 'check' }} text-white text-xs"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ ucfirst($transaction->status) }}</p>
                        <p class="text-xs text-gray-500">Status transaksi</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
