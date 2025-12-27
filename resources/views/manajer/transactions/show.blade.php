@extends('layouts.manajer')

@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')
@section('breadcrumb', 'Home / Riwayat Transaksi / Detail')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('manajer.transactions.index') }}" class="inline-flex items-center text-teal-600 hover:text-teal-800">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Riwayat
        </a>
    </div>

    <!-- Transaction Detail Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-teal-50 to-blue-50">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-file-alt text-teal-500 mr-2"></i>
                Detail Transaksi #{{ $transaction->id }}
            </h3>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">ID Transaksi</label>
                        <p class="text-lg font-semibold text-gray-900">#{{ $transaction->id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal</label>
                        <p class="text-lg text-gray-900">
                            <i class="fas fa-calendar-alt text-teal-500 mr-2"></i>
                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('d F Y, H:i') }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Tipe Transaksi</label>
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full
                            {{ $transaction->type === 'in' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            <i class="fas fa-{{ $transaction->type === 'in' ? 'arrow-down' : 'arrow-up' }} mr-2"></i>
                            {{ $transaction->type === 'in' ? 'Barang Masuk' : 'Barang Keluar' }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full
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
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Dibuat Oleh</label>
                        <p class="text-lg text-gray-900">
                            <i class="fas fa-user text-teal-500 mr-2"></i>
                            {{ $transaction->user->name ?? '-' }}
                        </p>
                    </div>

                    @if($transaction->assignedStaff)
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Ditugaskan Ke</label>
                            <p class="text-lg text-gray-900">
                                <i class="fas fa-user-check text-indigo-500 mr-2"></i>
                                {{ $transaction->assignedStaff->name }}
                            </p>
                        </div>
                    @else
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Jenis Input</label>
                            <span class="inline-block px-2 py-1 text-sm font-semibold bg-teal-100 text-teal-800 rounded">
                                <i class="fas fa-hand-paper mr-1"></i>Input Mandiri Staff
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Informasi Produk</label>
                        <div class="space-y-2">
                            <p class="text-lg font-bold text-gray-900">{{ $transaction->product->name }}</p>
                            <p class="text-sm text-gray-600">SKU: {{ $transaction->product->sku }}</p>
                            @if($transaction->product->category)
                                <span class="inline-block px-2 py-1 text-xs font-semibold bg-purple-100 text-purple-800 rounded">
                                    {{ $transaction->product->category->name }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="bg-teal-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Jumlah</label>
                        <p class="text-3xl font-bold text-teal-600">
                            {{ $transaction->quantity }} <span class="text-lg">unit</span>
                        </p>
                    </div>

                    @if($transaction->notes)
                        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                            <label class="block text-sm font-medium text-gray-600 mb-2">
                                <i class="fas fa-sticky-note mr-1"></i>Catatan
                            </label>
                            <p class="text-sm text-gray-700">{{ $transaction->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
