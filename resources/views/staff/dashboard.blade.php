@extends('layouts.staff')

@section('title', 'Staff Dashboard')
@section('page-title', 'Dashboard Staff')
@section('breadcrumb', 'Home / Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Barang Masuk Pending -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Barang Masuk Pending</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $pendingIncoming->count() }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-arrow-down text-green-500 text-2xl"></i>
            </div>
        </div>
        <a href="{{ route('staff.stock.in') }}" class="mt-4 block text-center text-sm text-green-600 hover:text-green-700 font-medium">
            Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    <!-- Barang Keluar Pending -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Barang Keluar Pending</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $pendingOutgoing->count() }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-arrow-up text-blue-500 text-2xl"></i>
            </div>
        </div>
        <a href="{{ route('staff.stock.out') }}" class="mt-4 block text-center text-sm text-blue-600 hover:text-blue-700 font-medium">
            Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-bolt text-yellow-500 mr-2"></i>
        Aksi Cepat
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('staff.stock.in') }}" class="flex items-center justify-center p-4 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors duration-200">
            <i class="fas fa-arrow-down text-2xl mr-3"></i>
            <span class="font-medium">Input Barang Masuk</span>
        </a>
        <a href="{{ route('staff.stock.out') }}" class="flex items-center justify-center p-4 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200">
            <i class="fas fa-arrow-up text-2xl mr-3"></i>
            <span class="font-medium">Input Barang Keluar</span>
        </a>
        <a href="{{ route('staff.stock.check') }}" class="flex items-center justify-center p-4 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition-colors duration-200">
            <i class="fas fa-clipboard-check text-2xl mr-3"></i>
            <span class="font-medium">Cek Stok</span>
        </a>
    </div>
</div>

<!-- Task List -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Barang Masuk yang Perlu Diperiksa -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-clipboard-check text-green-500 mr-2"></i>
                Barang Masuk - Perlu Diperiksa
            </h3>
            <p class="text-sm text-gray-600 mt-1">Barang masuk yang menunggu verifikasi</p>
        </div>
        <div class="p-6">
            @if($pendingIncoming->count() > 0)
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($pendingIncoming as $transaction)
                        <div class="border border-green-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded">
                                            MASUK
                                        </span>
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded">
                                            PENDING
                                        </span>
                                    </div>
                                    <h4 class="font-semibold text-gray-900">{{ $transaction->product->name }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">SKU: {{ $transaction->product->sku }}</p>
                                    <div class="mt-2 flex items-center text-sm text-gray-500">
                                        <i class="fas fa-box mr-2"></i>
                                        <span class="font-medium text-gray-700">{{ $transaction->quantity }} unit</span>
                                    </div>
                                    <div class="mt-1 flex items-center text-sm text-gray-500">
                                        <i class="fas fa-user mr-2"></i>
                                        <span>Oleh: {{ $transaction->user->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="mt-1 flex items-center text-sm text-gray-500">
                                        <i class="fas fa-calendar mr-2"></i>
                                        <span>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @if($transaction->notes)
                                        <div class="mt-2 p-2 bg-gray-50 rounded text-xs text-gray-600">
                                            <i class="fas fa-sticky-note mr-1"></i>
                                            {{ $transaction->notes }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <a href="{{ route('staff.stock.in') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">
                                    Periksa & Verifikasi <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('staff.stock.in') }}" class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-list mr-2"></i>
                        Lihat Semua Barang Masuk
                    </a>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                    <p class="text-gray-600 font-medium">Tidak ada barang masuk yang perlu diperiksa</p>
                    <p class="text-sm text-gray-500 mt-1">Semua barang masuk sudah diverifikasi</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Barang Keluar yang Perlu Disiapkan -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-box-open text-blue-500 mr-2"></i>
                Barang Keluar - Perlu Disiapkan
            </h3>
            <p class="text-sm text-gray-600 mt-1">Barang keluar yang menunggu persiapan</p>
        </div>
        <div class="p-6">
            @if($pendingOutgoing->count() > 0)
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($pendingOutgoing as $transaction)
                        <div class="border border-blue-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded">
                                            KELUAR
                                        </span>
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded">
                                            PENDING
                                        </span>
                                    </div>
                                    <h4 class="font-semibold text-gray-900">{{ $transaction->product->name }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">SKU: {{ $transaction->product->sku }}</p>
                                    <div class="mt-2 flex items-center text-sm text-gray-500">
                                        <i class="fas fa-box mr-2"></i>
                                        <span class="font-medium text-gray-700">{{ $transaction->quantity }} unit</span>
                                    </div>
                                    <div class="mt-1 flex items-center text-sm text-gray-500">
                                        <i class="fas fa-user mr-2"></i>
                                        <span>Oleh: {{ $transaction->user->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="mt-1 flex items-center text-sm text-gray-500">
                                        <i class="fas fa-calendar mr-2"></i>
                                        <span>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @if($transaction->notes)
                                        <div class="mt-2 p-2 bg-gray-50 rounded text-xs text-gray-600">
                                            <i class="fas fa-sticky-note mr-1"></i>
                                            {{ $transaction->notes }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <a href="{{ route('staff.stock.out') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                    Siapkan & Proses <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('staff.stock.out') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-list mr-2"></i>
                        Lihat Semua Barang Keluar
                    </a>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-check-circle text-blue-500 text-4xl mb-3"></i>
                    <p class="text-gray-600 font-medium">Tidak ada barang keluar yang perlu disiapkan</p>
                    <p class="text-sm text-gray-500 mt-1">Semua barang keluar sudah diproses</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- My Recent Transactions -->
<div class="bg-white rounded-lg shadow-md">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-history text-purple-500 mr-2"></i>
            Transaksi Saya Terakhir
        </h3>
    </div>
    <div class="p-6">
        @if($myTransactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($myTransactions as $transaction)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $transaction->product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $transaction->product->sku }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transaction->type === 'in' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $transaction->type === 'in' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
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
                                    {{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-600 text-center py-4">Belum ada transaksi</p>
        @endif
    </div>
</div>
@endsection
