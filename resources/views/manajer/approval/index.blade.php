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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                <div class="text-sm text-gray-500">SKU: {{ $item->product->sku }}</div>
                                @if($item->assignedStaff)
                                    <div class="text-xs text-purple-600 mt-1">
                                        <i class="fas fa-user-check"></i> Ditugaskan ke: {{ $item->assignedStaff->name }}
                                    </div>
                                @else
                                    <div class="text-xs text-blue-600 mt-1">
                                        <i class="fas fa-hand-paper"></i> Input mandiri staff
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $item->type === 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <i class="fas fa-arrow-{{ $item->type === 'in' ? 'down' : 'up' }} mr-1"></i>
                                    {{ $item->type === 'in' ? 'Masuk' : 'Keluar' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ $item->quantity }} unit
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->user->name ?? '-' }}
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
                                        <form action="{{ route('manajer.approval.approve', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-sm rounded-lg transition" title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <button onclick="openRejectModal({{ $item->id }}, 'transaction')" class="inline-flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition" title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @else
                        {{-- Stock Opname Row --}}
                        <tr class="hover:bg-gray-50 bg-blue-50/30">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->checked_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                <div class="text-sm text-gray-500">SKU: {{ $item->product->sku }}</div>
                                <div class="text-xs text-indigo-600 mt-1">
                                    <i class="fas fa-clipboard-check"></i> Stock Opname
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                    <i class="fas fa-clipboard-check mr-1"></i>
                                    Opname
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="text-gray-600">Sistem: <span class="font-semibold">{{ $item->system_stock }}</span></div>
                                    <div class="text-gray-600">Fisik: <span class="font-semibold">{{ $item->physical_stock }}</span></div>
                                    <div class="{{ $item->difference > 0 ? 'text-green-600' : ($item->difference < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                        Selisih: <span class="font-bold">{{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->user->name ?? '-' }}
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
                                    <div class="flex items-center space-x-2">
                                        <form action="{{ route('manajer.approval.opname.approve', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-sm rounded-lg transition" title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <button onclick="openRejectModal({{ $item->id }}, 'opname')" class="inline-flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition" title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
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
</script>
@endpush
@endsection
