@extends('layouts.staff')

@section('title', 'Riwayat Transaksi')
@section('page-title', 'Riwayat Transaksi')
@section('breadcrumb', 'Home / Riwayat Transaksi')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Transaksi</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalTransactions }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-list text-purple-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Barang Masuk</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalIn }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-arrow-down text-green-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Barang Keluar</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalOut }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-arrow-up text-blue-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Pending</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $pending }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-clock text-yellow-500 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-filter text-blue-500 mr-2"></i>
        Filter Riwayat
    </h3>
    <form method="GET" action="{{ route('staff.transactions.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <!-- Search -->
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari Produk</label>
            <input type="text" id="search" name="search" value="{{ request('search') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Nama atau SKU...">
        </div>

        <!-- Start Date -->
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <!-- End Date -->
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <!-- Type Filter -->
        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
            <select id="type" name="type"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Tipe</option>
                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Barang Masuk</option>
                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Barang Keluar</option>
            </select>
        </div>

        <!-- Status Filter -->
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select id="status" name="status"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                <option value="dikeluarkan" {{ request('status') == 'dikeluarkan' ? 'selected' : '' }}>Dikeluarkan</option>
                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <!-- Action Buttons (Full Width) -->
        <div class="md:col-span-5 flex gap-2">
            <button type="submit" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-search mr-2"></i>Filter
            </button>
            <a href="{{ route('staff.transactions.index') }}" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-redo mr-2"></i>Reset
            </a>
        </div>
    </form>
</div>

<!-- Transaction List -->
<div class="bg-white rounded-lg shadow-md">
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-blue-50">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-history text-purple-500 mr-2"></i>
            Daftar Riwayat Transaksi
        </h3>
        <p class="text-sm text-gray-600 mt-1">Transaksi yang Anda buat atau ditugaskan kepada Anda</p>
    </div>
    <div class="p-6">
        @if($transactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transactions as $transaction)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $transaction->product->name }}</div>
                                            <div class="text-sm text-gray-500">SKU: {{ $transaction->product->sku }}</div>
                                            @if($transaction->product->category)
                                                <span class="mt-1 inline-block px-2 py-0.5 text-xs font-semibold bg-purple-100 text-purple-800 rounded">
                                                    {{ $transaction->product->category->name }}
                                                </span>
                                            @endif
                                            <!-- Label: Input Mandiri atau Assignment -->
                                            <div class="mt-1">
                                                @if($transaction->assigned_to)
                                                    <span class="inline-block px-2 py-0.5 text-xs font-semibold bg-indigo-100 text-indigo-800 rounded">
                                                        <i class="fas fa-user-check mr-1"></i>Ditugaskan
                                                    </span>
                                                @else
                                                    <span class="inline-block px-2 py-0.5 text-xs font-semibold bg-teal-100 text-teal-800 rounded">
                                                        <i class="fas fa-hand-paper mr-1"></i>Input Mandiri
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $transaction->type === 'in' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        <i class="fas fa-{{ $transaction->type === 'in' ? 'arrow-down' : 'arrow-up' }} mr-1"></i>
                                        {{ $transaction->type === 'in' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $transaction->quantity }} unit</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($transaction->status === 'diterima' || $transaction->status === 'dikeluarkan') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        @if($transaction->status === 'pending')
                                            <i class="fas fa-clock mr-1"></i>
                                        @elseif($transaction->status === 'diterima' || $transaction->status === 'dikeluarkan')
                                            <i class="fas fa-check-circle mr-1"></i>
                                        @else
                                            <i class="fas fa-times-circle mr-1"></i>
                                        @endif
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('staff.transactions.show', $transaction->id) }}"
                                       class="inline-flex items-center px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded transition-colors duration-200">
                                        <i class="fas fa-eye mr-1"></i>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
                <div class="mt-6">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <i class="fas fa-inbox text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Riwayat</h3>
                <p class="text-gray-500">Belum ada transaksi yang ditemukan dengan filter yang dipilih</p>
            </div>
        @endif
    </div>
</div>
@endsection
