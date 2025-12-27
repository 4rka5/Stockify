@extends('layouts.manajer')

@section('title', 'Stock Opname - Monitoring')
@section('page-title', 'Stock Opname - Monitoring')
@section('breadcrumb', 'Home / Stock Opname')

@section('content')
<div class="mb-6">
    <h3 class="text-lg font-semibold text-gray-800">Monitoring Stock Opname</h3>
    <p class="text-sm text-gray-600">Monitoring hasil pengecekan fisik dari staff gudang</p>
</div>

<!-- Statistics -->
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
                <p class="text-gray-600 text-sm font-medium">Approved</p>
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
                <p class="text-gray-600 text-sm font-medium">Rejected</p>
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
    <form action="{{ route('manajer.stock-opname.index') }}" method="GET" class="flex gap-4">
        <div class="flex-1">
            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">
            <i class="fas fa-filter mr-2"></i>Filter
        </button>
        <a href="{{ route('manajer.stock-opname.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">
            <i class="fas fa-redo"></i>
        </a>
    </form>
</div>

<!-- Opname List -->
<div class="bg-white rounded-lg shadow-md">
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-teal-50 to-blue-50">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-clipboard-list text-teal-500 mr-2"></i>
            Daftar Stock Opname
        </h3>
    </div>
    <div class="p-6">
        @if($opnames->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Staff</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stok Sistem</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stok Fisik</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Selisih</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($opnames as $opname)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">{{ $opname->product->name }}</div>
                                        <div class="text-gray-500">{{ $opname->product->category->name ?? '-' }}</div>
                                        @if($opname->notes)
                                            <div class="text-xs text-gray-600 mt-1">
                                                <i class="fas fa-sticky-note mr-1"></i>{{ $opname->notes }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-600">
                                    {{ $opname->user->name }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-lg font-bold text-gray-800">{{ $opname->system_stock }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-lg font-bold text-gray-800">{{ $opname->physical_stock }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 text-sm font-bold rounded-full {{ $opname->difference > 0 ? 'bg-green-100 text-green-800' : ($opname->difference < 0 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ $opname->difference > 0 ? '+' : '' }}{{ $opname->difference }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-600">
                                    {{ $opname->checked_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 text-xs rounded-full {{ $opname->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($opname->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($opname->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($opname->status === 'pending')
                                        <div class="flex justify-center gap-2">
                                            <form action="{{ route('manajer.stock-opname.approve', $opname->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Setujui penyesuaian stok ini?')"
                                                        class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-xs rounded">
                                                    <i class="fas fa-check mr-1"></i>Setujui
                                                </button>
                                            </form>
                                            <button onclick="showRejectModal({{ $opname->id }})"
                                                    class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded">
                                                <i class="fas fa-times mr-1"></i>Tolak
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-500">
                                            {{ $opname->approver->name ?? '-' }}<br>
                                            {{ $opname->approved_at ? $opname->approved_at->format('d/m/Y H:i') : '-' }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $opnames->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-clipboard-list text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Data Stock Opname</h3>
                <p class="text-gray-500">Belum ada pengecekan fisik dari staff</p>
            </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                Tolak Stock Opname
            </h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label for="reject_reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Alasan Penolakan
                </label>
                <textarea id="reject_reason" name="reason" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                    placeholder="Masukkan alasan penolakan..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()"
                    class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-lg">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg">
                    Tolak
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showRejectModal(opnameId) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        if (modal && form) {
            form.action = `/manajer/stock-opname/${opnameId}/reject`;
            modal.classList.remove('hidden');
        }
    }

    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        const reasonField = document.getElementById('reject_reason');
        if (modal) {
            modal.classList.add('hidden');
            if (reasonField) reasonField.value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('rejectModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeRejectModal();
                }
            });
        }
    });
</script>
@endpush
