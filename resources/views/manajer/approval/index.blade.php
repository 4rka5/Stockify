@extends('layouts.manajer')

@section('title', 'Approval')
@section('page-title', 'Approval Persetujuan')
@section('breadcrumb', 'Home / Approval')

@section('content')
<div class="mb-6">
    <h3 class="text-lg font-semibold text-gray-800">Approval Transaksi & Stock Opname</h3>
    <p class="text-sm text-gray-600">Kelola persetujuan transaksi barang dan stock opname dari staff</p>
</div>

<!-- Stats Card -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Pending</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $pendingCount }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-clock text-yellow-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Disetujui</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $approvedCount }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-check-circle text-green-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Ditolak</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $rejectedCount }}</p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-times-circle text-red-500 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form action="{{ route('manajer.approval.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-filter mr-1"></i> Status
            </label>
            <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                <option value="dikeluarkan" {{ request('status') == 'dikeluarkan' ? 'selected' : '' }}>Dikeluarkan</option>
                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <div class="flex-1 min-w-[200px]">
            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-exchange-alt mr-1"></i> Tipe
            </label>
            <select id="type" name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                <option value="">Semua Tipe</option>
                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Barang Masuk</option>
                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Barang Keluar</option>
                <option value="opname" {{ request('type') == 'opname' ? 'selected' : '' }}>Stock Opname</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition">
                <i class="fas fa-search mr-2"></i>
                Filter
            </button>
            <a href="{{ route('manajer.approval.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Saat Ini</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Akhir</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($items as $item)
                    @if($item instanceof \App\Models\StockTransaction)
                        {{-- Transaction Row --}}
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                #{{ $item->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($item->date)->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                <div class="text-sm text-gray-500">SKU: {{ $item->product->sku }}</div>
                                @if($item->product->category)
                                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-semibold bg-purple-100 text-purple-800 rounded">
                                        {{ $item->product->category->name }}
                                    </span>
                                @endif
                                @if($item->assignedStaff)
                                    <div class="text-xs text-purple-600 mt-1">
                                        <i class="fas fa-user-check"></i> Ditugaskan ke: {{ $item->assignedStaff->name }}
                                    </div>
                                @else
                                    <div class="text-xs text-blue-600 mt-1">
                                        <i class="fas fa-hand-paper"></i> Input mandiri staff
                                    </div>
                                @endif
                                @if($item->supplier)
                                    <div class="text-xs text-teal-600 mt-1">
                                        <i class="fas fa-truck"></i> Supplier: <span class="font-semibold">{{ $item->supplier->name }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $item->product->stock_quantity }} unit</div>
                                <div class="text-xs text-gray-500">Stok terkini</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $item->type === 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <i class="fas fa-arrow-{{ $item->type === 'in' ? 'down' : 'up' }} mr-1"></i>
                                    {{ $item->type === 'in' ? 'Masuk' : 'Keluar' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $item->quantity }} unit</div>
                                <div class="text-xs {{ $item->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $item->type === 'in' ? '+' : '-' }}{{ $item->quantity }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $finalStock = $item->type === 'in'
                                        ? $item->product->stock_quantity + $item->quantity
                                        : $item->product->stock_quantity - $item->quantity;
                                @endphp
                                <div class="text-sm font-bold {{ $finalStock < 0 ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $finalStock }} unit
                                </div>
                                <div class="text-xs text-gray-500">Setelah transaksi</div>
                                @if($finalStock < 0)
                                    <div class="text-xs text-red-600 mt-1">
                                        <i class="fas fa-exclamation-triangle"></i> Stok negatif!
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->user->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $item->user->role ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->status === 'pending')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Pending
                                    </span>
                                @elseif($item->status === 'diterima' || $item->status === 'dikeluarkan')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        {{ ucfirst($item->status) }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        {{ ucfirst($item->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $item->notes ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($item->status === 'pending')
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('manajer.transactions.show', $item->id) }}" class="inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-lg transition" title="Lihat Detail">
                                            <i class="fas fa-eye mr-1"></i>
                                            Detail
                                        </a>
                                        <form action="{{ route('manajer.approval.approve', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-sm rounded-lg transition" title="Setujui">
                                                <i class="fas fa-check mr-1"></i>
                                                Setujui
                                            </button>
                                        </form>
                                        <button onclick="openRejectModal({{ $item->id }}, 'transaction')" class="inline-flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition" title="Tolak">
                                            <i class="fas fa-times mr-1"></i>
                                            Tolak
                                        </button>
                                    </div>
                                @else
                                    <a href="{{ route('manajer.transactions.show', $item->id) }}" class="inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-lg transition" title="Lihat Detail">
                                        <i class="fas fa-eye mr-1"></i>
                                        Detail
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @else
                        {{-- Stock Opname Row --}}
                        <tr class="hover:bg-gray-50 bg-blue-50/30">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                #OP-{{ $item->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->checked_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $item->checked_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                <div class="text-sm text-gray-500">SKU: {{ $item->product->sku }}</div>
                                @if($item->product->category)
                                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-semibold bg-purple-100 text-purple-800 rounded">
                                        {{ $item->product->category->name }}
                                    </span>
                                @endif
                                <div class="text-xs text-indigo-600 mt-1">
                                    <i class="fas fa-clipboard-check"></i> Stock Opname
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-blue-900">{{ $item->system_stock }} unit</div>
                                <div class="text-xs text-gray-500">Stok sistem</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                    <i class="fas fa-clipboard-check mr-1"></i>
                                    Opname
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-indigo-900">{{ $item->physical_stock }} unit</div>
                                <div class="text-xs text-gray-500">Stok fisik</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold {{ $item->difference > 0 ? 'text-green-600' : ($item->difference < 0 ? 'text-red-600' : 'text-gray-900') }}">
                                    {{ $item->physical_stock }} unit
                                </div>
                                <div class="text-xs {{ $item->difference > 0 ? 'text-green-600' : ($item->difference < 0 ? 'text-red-600' : 'text-gray-500') }}">
                                    Selisih: {{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->user->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $item->user->role ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->status === 'pending')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Pending
                                    </span>
                                @elseif($item->status === 'approved')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Disetujui
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $item->notes ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($item->status === 'pending')
                                    <div class="flex flex-col gap-2">
                                        <div class="flex items-center space-x-2">
                                            <form action="{{ route('manajer.approval.opname.approve', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-sm rounded-lg transition" title="Setujui">
                                                    <i class="fas fa-check mr-1"></i>
                                                    Setujui
                                                </button>
                                            </form>
                                            <button onclick="openRejectModal({{ $item->id }}, 'opname')" class="inline-flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition" title="Tolak">
                                                <i class="fas fa-times mr-1"></i>
                                                Tolak
                                            </button>
                                        </div>
                                        <button onclick="showOpnameDetail({{ $item->id }}, '{{ $item->product->name }}', '{{ $item->product->sku }}', {{ $item->system_stock }}, {{ $item->physical_stock }}, {{ $item->difference }}, '{{ $item->notes }}', '{{ $item->user->name }}', '{{ $item->checked_at->format('d M Y H:i') }}')" class="inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-lg transition w-full justify-center" title="Lihat Detail">
                                            <i class="fas fa-eye mr-1"></i>
                                            Detail
                                        </button>
                                    </div>
                                @else
                                    <button onclick="showOpnameDetail({{ $item->id }}, '{{ $item->product->name }}', '{{ $item->product->sku }}', {{ $item->system_stock }}, {{ $item->physical_stock }}, {{ $item->difference }}, '{{ $item->notes }}', '{{ $item->user->name }}', '{{ $item->checked_at->format('d M Y H:i') }}')" class="inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-lg transition" title="Lihat Detail">
                                        <i class="fas fa-eye mr-1"></i>
                                        Detail
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">Tidak ada data approval</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 bg-gray-50">
        @if($total > $perPage)
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-700">
                    Menampilkan {{ ($page - 1) * $perPage + 1 }} - {{ min($page * $perPage, $total) }} dari {{ $total }} data
                </div>
                <div class="flex gap-2">
                    @if($page > 1)
                        <a href="{{ route('manajer.approval.index', array_merge(request()->query(), ['page' => $page - 1])) }}"
                           class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded">
                            Previous
                        </a>
                    @endif
                    @if($page * $perPage < $total)
                        <a href="{{ route('manajer.approval.index', array_merge(request()->query(), ['page' => $page + 1])) }}"
                           class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded">
                            Next
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tolak <span id="rejectType">Item</span></h3>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan (Opsional)</label>
                    <textarea id="reason" name="reason" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Masukkan alasan penolakan..."></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                        <i class="fas fa-times mr-2"></i>
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Stock Opname Detail Modal -->
<div id="opnameDetailModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-indigo-50 to-blue-50 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-clipboard-check text-indigo-500 mr-2"></i>
                    Detail Stock Opname
                </h3>
                <button onclick="closeOpnameDetailModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <!-- Product Info -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h4 class="text-sm font-semibold text-gray-600 mb-3">
                    <i class="fas fa-box mr-1"></i>
                    Informasi Produk
                </h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Nama Produk</p>
                        <p id="opnameProductName" class="text-sm font-bold text-gray-900"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">SKU</p>
                        <p id="opnameProductSku" class="text-sm font-mono text-gray-900"></p>
                    </div>
                </div>
            </div>

            <!-- Stock Comparison -->
            <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-600 mb-3">
                    <i class="fas fa-balance-scale mr-1"></i>
                    Perbandingan Stok
                </h4>
                <div class="grid grid-cols-3 gap-4">
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-xs text-blue-600 mb-2">Stok Sistem</p>
                        <p id="opnameSystemStock" class="text-2xl font-bold text-blue-700"></p>
                        <p class="text-xs text-gray-500 mt-1">unit</p>
                    </div>
                    <div class="p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                        <p class="text-xs text-indigo-600 mb-2">Stok Fisik</p>
                        <p id="opnamePhysicalStock" class="text-2xl font-bold text-indigo-700"></p>
                        <p class="text-xs text-gray-500 mt-1">unit</p>
                    </div>
                    <div class="p-4 rounded-lg border" id="opnameDifferenceCard">
                        <p class="text-xs mb-2" id="opnameDifferenceLabel">Selisih</p>
                        <p id="opnameDifference" class="text-2xl font-bold"></p>
                        <p class="text-xs text-gray-500 mt-1">unit</p>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500 mb-1">
                        <i class="fas fa-user mr-1"></i>
                        Diperiksa Oleh
                    </p>
                    <p id="opnameUserName" class="text-sm font-semibold text-gray-900"></p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500 mb-1">
                        <i class="fas fa-calendar mr-1"></i>
                        Tanggal Pemeriksaan
                    </p>
                    <p id="opnameCheckedAt" class="text-sm font-semibold text-gray-900"></p>
                </div>
            </div>

            <!-- Notes -->
            <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200" id="opnameNotesSection">
                <p class="text-xs text-yellow-700 font-semibold mb-2">
                    <i class="fas fa-sticky-note mr-1"></i>
                    Catatan
                </p>
                <p id="opnameNotes" class="text-sm text-gray-700"></p>
            </div>
        </div>
        <div class="sticky bottom-0 bg-gray-50 px-6 py-4 border-t border-gray-200">
            <button onclick="closeOpnameDetailModal()" class="w-full px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                <i class="fas fa-times mr-2"></i>
                Tutup
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openRejectModal(itemId, type) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    const typeLabel = document.getElementById('rejectType');

    if (type === 'opname') {
        form.action = '{{ route("manajer.approval.opname.reject", ":id") }}'.replace(':id', itemId);
        typeLabel.textContent = 'Stock Opname';
    } else {
        form.action = '{{ route("manajer.approval.reject", ":id") }}'.replace(':id', itemId);
        typeLabel.textContent = 'Transaksi';
    }

    modal.classList.remove('hidden');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
    document.getElementById('reason').value = '';
}

// Close modal on outside click
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});

function showOpnameDetail(id, productName, productSku, systemStock, physicalStock, difference, notes, userName, checkedAt) {
    document.getElementById('opnameProductName').textContent = productName;
    document.getElementById('opnameProductSku').textContent = productSku;
    document.getElementById('opnameSystemStock').textContent = systemStock;
    document.getElementById('opnamePhysicalStock').textContent = physicalStock;
    document.getElementById('opnameDifference').textContent = (difference > 0 ? '+' : '') + difference;
    document.getElementById('opnameUserName').textContent = userName;
    document.getElementById('opnameCheckedAt').textContent = checkedAt;

    // Style difference card based on value
    const diffCard = document.getElementById('opnameDifferenceCard');
    const diffLabel = document.getElementById('opnameDifferenceLabel');
    const diffValue = document.getElementById('opnameDifference');

    if (difference > 0) {
        diffCard.className = 'p-4 bg-green-50 rounded-lg border border-green-200';
        diffLabel.className = 'text-xs text-green-600 mb-2';
        diffValue.className = 'text-2xl font-bold text-green-700';
    } else if (difference < 0) {
        diffCard.className = 'p-4 bg-red-50 rounded-lg border border-red-200';
        diffLabel.className = 'text-xs text-red-600 mb-2';
        diffValue.className = 'text-2xl font-bold text-red-700';
    } else {
        diffCard.className = 'p-4 bg-gray-50 rounded-lg border border-gray-200';
        diffLabel.className = 'text-xs text-gray-600 mb-2';
        diffValue.className = 'text-2xl font-bold text-gray-700';
    }

    // Show or hide notes section
    const notesSection = document.getElementById('opnameNotesSection');
    const notesElement = document.getElementById('opnameNotes');
    if (notes && notes !== '-' && notes !== '') {
        notesElement.textContent = notes;
        notesSection.classList.remove('hidden');
    } else {
        notesSection.classList.add('hidden');
    }

    document.getElementById('opnameDetailModal').classList.remove('hidden');
}

function closeOpnameDetailModal() {
    document.getElementById('opnameDetailModal').classList.add('hidden');
}

// Close opname detail modal on outside click
document.getElementById('opnameDetailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeOpnameDetailModal();
    }
});
</script>
@endpush
@endsection
