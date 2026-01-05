@extends('layouts.staff')

@section('title', 'Barang Masuk')
@section('page-title', 'Barang Masuk')
@section('breadcrumb', 'Home / Stok / Barang Masuk')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Perlu Konfirmasi</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $pendingTransactions->count() }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-clock text-yellow-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Dikonfirmasi Hari Ini</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $confirmedToday }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-check-circle text-green-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Bulan Ini</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalThisMonth }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-calendar text-blue-500 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Add New Transaction Form -->
<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-blue-50">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-plus-circle text-purple-500 mr-2"></i>
            Input Barang Masuk Baru
        </h3>
        <p class="text-sm text-gray-600 mt-1">Langsung input transaksi barang masuk tanpa menunggu manajer</p>
    </div>
    <div class="p-6">
        <form action="{{ route('staff.stock.in.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Catatan:</strong> Transaksi yang Anda input akan diajukan ke manajer untuk disetujui.
                            Stok akan otomatis terupdate setelah manajer menyetujui transaksi.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Produk <span class="text-red-500">*</span>
                    </label>
                    <select name="product_id" id="product_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">
                                {{ $product->name }} ({{ $product->sku }}) - Stok: {{ $product->current_stock }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-truck mr-1"></i>
                        Supplier (Opsional)
                    </label>
                    <select name="supplier_id" id="supplier_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">
                                {{ $supplier->name }} - {{ $supplier->phone }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="quantity" id="quantity" min="1" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                           placeholder="0">
                    @error('quantity')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan (Opsional)
                    </label>
                    <input type="text" name="notes" id="notes"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                           placeholder="Contoh: No. PO 123, lokasi penyimpanan, dll">
                    @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-3 bg-purple-500 hover:bg-purple-600 text-white font-medium rounded-lg transition-colors duration-200 flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Barang Masuk
                </button>
                <button type="reset"
                        class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-redo mr-2"></i>
                    Reset
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Pending Transactions to Confirm -->
<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-yellow-50 to-green-50">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-clipboard-check text-yellow-500 mr-2"></i>
            Barang Masuk - Perlu Konfirmasi (dari Manajer)
        </h3>
        <p class="text-sm text-gray-600 mt-1">Periksa dan konfirmasi penerimaan barang yang ditugaskan manajer</p>
    </div>
    <div class="p-6">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if($pendingTransactions->count() > 0)
            <div class="space-y-4">
                @foreach($pendingTransactions as $transaction)
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow duration-200">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <!-- Left: Product Info -->
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded">
                                        BARANG MASUK
                                    </span>
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded">
                                        PENDING
                                    </span>
                                </div>

                                <h4 class="text-lg font-bold text-gray-900 mb-2">{{ $transaction->product->name }}</h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-barcode w-6 mr-2 text-gray-400"></i>
                                        <span class="font-medium">SKU:</span>
                                        <span class="ml-2">{{ $transaction->product->sku }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-boxes w-6 mr-2 text-gray-400"></i>
                                        <span class="font-medium">Jumlah:</span>
                                        <span class="ml-2 font-bold text-green-600">{{ $transaction->quantity }} unit</span>
                                    </div>
                                    @if($transaction->supplier)
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fas fa-truck w-6 mr-2 text-gray-400"></i>
                                            <span class="font-medium">Supplier:</span>
                                            <span class="ml-2 text-blue-600 font-semibold">{{ $transaction->supplier->name }}</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-user w-6 mr-2 text-gray-400"></i>
                                        <span class="font-medium">Dibuat oleh:</span>
                                        <span class="ml-2">{{ $transaction->user->name }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-calendar w-6 mr-2 text-gray-400"></i>
                                        <span class="font-medium">Tanggal:</span>
                                        <span class="ml-2">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>

                                @if($transaction->notes)
                                    <div class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <p class="text-xs font-semibold text-gray-600 mb-1">
                                            <i class="fas fa-sticky-note mr-1"></i>Catatan:
                                        </p>
                                        <p class="text-sm text-gray-700">{{ $transaction->notes }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Right: Action Buttons -->
                            <div class="flex flex-col gap-2 lg:min-w-[200px]">
                                <!-- Confirm Button -->
                                <button onclick="confirmTransaction({{ $transaction->id }}, 'diterima')"
                                        class="w-full px-4 py-3 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors duration-200 flex items-center justify-center">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Konfirmasi Diterima
                                </button>

                                <!-- Reject Button -->
                                <button onclick="showRejectModal({{ $transaction->id }})"
                                        class="w-full px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors duration-200 flex items-center justify-center">
                                    <i class="fas fa-times-circle mr-2"></i>
                                    Tolak
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($pendingTransactions->hasPages())
                <div class="mt-6">
                    {{ $pendingTransactions->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <i class="fas fa-check-circle text-green-500 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Semua Barang Sudah Dikonfirmasi</h3>
                <p class="text-gray-500">Tidak ada barang masuk yang perlu dikonfirmasi saat ini</p>
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
                Tolak Barang Masuk
            </h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="rejectForm" method="POST">
            @csrf
            <input type="hidden" name="status" value="ditolak">
            <div class="mb-4">
                <label for="reject_reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea id="reject_reason" name="reason" rows="4" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    placeholder="Masukkan alasan penolakan barang..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()"
                    class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-lg transition-colors duration-200">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors duration-200">
                    Tolak Barang
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function confirmTransaction(transactionId, status) {
        if (confirm('Apakah Anda yakin barang telah diterima dengan baik?')) {
            // Create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/staff/stock/confirm/${transactionId}`;

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Add status
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;
            form.appendChild(statusInput);

            document.body.appendChild(form);
            form.submit();
        }
    }

    function showRejectModal(transactionId) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        if (modal && form) {
            form.action = `/staff/stock/confirm/${transactionId}`;
            modal.classList.remove('hidden');
        } else {
            console.error('Modal or form not found');
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

    // Wait for DOM to be ready
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
