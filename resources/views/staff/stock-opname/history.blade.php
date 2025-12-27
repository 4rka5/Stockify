@extends('layouts.staff')

@section('title', 'Riwayat Stock Opname')
@section('page-title', 'Riwayat Stock Opname')
@section('breadcrumb', 'Home / Stock Opname / Riwayat')

@section('content')
<div class="mb-6">
    <h3 class="text-lg font-semibold text-gray-800">Riwayat Stock Opname</h3>
    <p class="text-sm text-gray-600">Daftar pengecekan fisik yang pernah Anda lakukan</p>
</div>

<!-- Back Button -->
<div class="mb-6">
    <a href="{{ route('staff.stock-opname.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Stock Opname
    </a>
</div>

<!-- Opname List -->
<div class="bg-white rounded-lg shadow-md">
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-history text-purple-500 mr-2"></i>
            Riwayat Pengecekan Anda
        </h3>
    </div>
    <div class="p-6">
        @if($opnames->count() > 0)
            <div class="space-y-4">
                @foreach($opnames as $opname)
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <!-- Left: Product Info -->
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="px-3 py-1 text-xs rounded-full {{ $opname->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($opname->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $opname->status === 'pending' ? 'PENDING' : ($opname->status === 'approved' ? 'DISETUJUI' : 'DITOLAK') }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $opname->checked_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>

                                <h4 class="text-lg font-bold text-gray-900 mb-2">{{ $opname->product->name }}</h4>

                                <div class="text-sm text-gray-600 mb-3">
                                    <span class="font-medium">Kategori:</span> {{ $opname->product->category->name ?? '-' }}
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 bg-gray-50 rounded-lg p-4">
                                    <div class="text-center">
                                        <p class="text-xs text-gray-600 mb-1">Stok Sistem</p>
                                        <p class="text-2xl font-bold text-gray-800">{{ $opname->system_stock }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-xs text-gray-600 mb-1">Stok Fisik</p>
                                        <p class="text-2xl font-bold text-purple-600">{{ $opname->physical_stock }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-xs text-gray-600 mb-1">Selisih</p>
                                        <p class="text-2xl font-bold {{ $opname->difference > 0 ? 'text-green-600' : ($opname->difference < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                            {{ $opname->difference > 0 ? '+' : '' }}{{ $opname->difference }}
                                        </p>
                                    </div>
                                </div>

                                @if($opname->notes)
                                    <div class="mt-3 p-3 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                                        <p class="text-xs font-semibold text-yellow-800 mb-1">
                                            <i class="fas fa-sticky-note mr-1"></i>Catatan Anda:
                                        </p>
                                        <p class="text-sm text-yellow-900">{{ $opname->notes }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Right: Status Info -->
                            <div class="lg:min-w-[200px] bg-gray-50 rounded-lg p-4">
                                @if($opname->status === 'pending')
                                    <div class="text-center">
                                        <i class="fas fa-clock text-yellow-500 text-3xl mb-2"></i>
                                        <p class="text-sm font-medium text-gray-700">Menunggu Persetujuan</p>
                                        <p class="text-xs text-gray-500 mt-1">Manajer akan memeriksa data Anda</p>
                                    </div>
                                @elseif($opname->status === 'approved')
                                    <div class="text-center">
                                        <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                                        <p class="text-sm font-medium text-gray-700 mb-2">Disetujui</p>
                                        <p class="text-xs text-gray-600">Oleh: {{ $opname->approver->name ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $opname->approved_at ? $opname->approved_at->format('d/m/Y H:i') : '-' }}</p>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <i class="fas fa-times-circle text-red-500 text-3xl mb-2"></i>
                                        <p class="text-sm font-medium text-gray-700 mb-2">Ditolak</p>
                                        <p class="text-xs text-gray-600">Oleh: {{ $opname->approver->name ?? '-' }}</p>
                                        <p class="text-xs text-gray-500 mb-2">{{ $opname->approved_at ? $opname->approved_at->format('d/m/Y H:i') : '-' }}</p>
                                        @if(str_contains($opname->notes, 'Ditolak:'))
                                            <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded text-left">
                                                <p class="text-xs text-red-800">
                                                    {{ str_replace('Ditolak:', '', substr($opname->notes, strrpos($opname->notes, 'Ditolak:'))) }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $opnames->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-clipboard-list text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Riwayat</h3>
                <p class="text-gray-500 mb-4">Anda belum melakukan pengecekan stock opname</p>
                <a href="{{ route('staff.stock-opname.index') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                    <i class="fas fa-clipboard-check mr-2"></i>
                    Mulai Stock Opname
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
