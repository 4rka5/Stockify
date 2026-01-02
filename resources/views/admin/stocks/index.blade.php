@extends('layouts.admin')

@section('title', 'Manajemen Stok')
@section('page-title', 'Manajemen Stok')
@section('breadcrumb', 'Home / Transaksi Stok')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-lg font-semibold text-gray-800">Transaksi Stok</h3>
        <p class="text-sm text-gray-600">Kelola semua transaksi stok masuk dan keluar</p>
    </div>
    <a href="{{ route('admin.stock-transactions.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
        <i class="fas fa-plus mr-2"></i>
        Tambah Transaksi
    </a>
</div>

<!-- Stats Card -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Transaksi</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $transactions->total() }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-exchange-alt text-blue-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Stok Masuk</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">
                    {{ $transactions->where('type', 'in')->where('status', 'diterima')->count() }}
                </p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-arrow-down text-green-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Stok Keluar</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">
                    {{ $transactions->where('type', 'out')->where('status', 'dikeluarkan')->count() }}
                </p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-arrow-up text-red-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Pending</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">
                    {{ $transactions->where('status', 'pending')->count() }}
                </p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-clock text-yellow-500 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form action="{{ route('admin.stock-transactions.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-filter mr-1"></i> Tipe
            </label>
            <select id="type" name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Tipe</option>
                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stok Masuk</option>
                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stok Keluar</option>
            </select>
        </div>

        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-info-circle mr-1"></i> Status
            </label>
            <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                <option value="pending_product_approval" {{ request('status') == 'pending_product_approval' ? 'selected' : '' }}>Pending Approval</option>
                <option value="dikeluarkan" {{ request('status') == 'dikeluarkan' ? 'selected' : '' }}>Dikeluarkan</option>
                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <div>
            <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-box mr-1"></i> Produk
            </label>
            <select id="product_id" name="product_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Produk</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-search mr-2"></i>
                Filter
            </button>
            <a href="{{ route('admin.stock-transactions.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                <i class="fas fa-redo mr-2"></i>
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Transactions Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($transactions as $index => $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ ($transactions->currentPage() - 1) * $transactions->perPage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($transaction->product->image)
                                        <img class="h-10 w-10 rounded-lg object-cover" src="{{ Storage::url($transaction->product->image) }}" alt="{{ $transaction->product->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-box text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $transaction->product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $transaction->product->sku }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $transaction->type === 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas fa-arrow-{{ $transaction->type === 'in' ? 'down' : 'up' }} mr-1"></i>
                                {{ $transaction->type === 'in' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            {{ $transaction->quantity }} unit
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'diterima' => 'bg-green-100 text-green-800',
                                    'dikeluarkan' => 'bg-blue-100 text-blue-800',
                                    'ditolak' => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$transaction->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->user->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.stock-transactions.show', $transaction->id) }}" class="inline-flex items-center px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-sm rounded-lg transition" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.stock-transactions.edit', $transaction->id) }}" class="inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-lg transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.stock-transactions.destroy', $transaction->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">Tidak ada data transaksi stok</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 bg-gray-50">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
