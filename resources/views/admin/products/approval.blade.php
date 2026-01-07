@extends('layouts.admin')

@section('title', 'Approval Produk')
@section('page-title', 'Approval Produk')
@section('breadcrumb', 'Home / Produk / Approval')

@section('content')
<div class="mb-6">
    <h3 class="text-lg font-semibold text-gray-800">Approval Produk Baru</h3>
    <p class="text-sm text-gray-600">Kelola pengajuan produk baru dari manajer</p>
</div>

<!-- Stats Card -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Menunggu Approval</p>
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
    <form action="{{ route('admin.products.approval') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-search mr-1"></i> Cari Produk
            </label>
            <input type="text" id="search" name="search" value="{{ request('search') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Cari berdasarkan nama atau SKU...">
        </div>

        <div class="w-full md:w-48">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-filter mr-1"></i> Status
            </label>
            <select id="status" name="status"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-search mr-2"></i>
                Filter
            </button>
            <a href="{{ route('admin.products.approval') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                <i class="fas fa-redo mr-2"></i>
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Products Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    @if($products->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Diajukan Oleh</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($products as $product)
                        <tr class="hover:bg-blue-50 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                             alt="{{ $product->name }}"
                                             class="w-16 h-16 rounded-lg object-cover shadow-sm flex-shrink-0">
                                    @else
                                        <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-image text-gray-400 text-xl"></i>
                                        </div>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <div class="text-sm font-semibold text-gray-900 truncate">{{ $product->name }}</div>
                                        <div class="text-xs text-gray-500">SKU: {{ $product->sku }}</div>
                                        @php
                                            $pendingTask = $product->stockTransactions()->where('status', 'pending_product_approval')->first();
                                        @endphp
                                        @if($pendingTask)
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700 font-medium">
                                                    <i class="fas fa-tasks mr-1"></i> Tugas Pending
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                    {{ $product->category?->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $product->supplier?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="space-y-1">
                                    <div>
                                        <span class="text-xs font-medium text-gray-500">Beli:</span>
                                        <span class="text-sm font-semibold text-gray-900">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-xs font-medium text-gray-500">Jual:</span>
                                        <span class="text-sm font-semibold text-green-600">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $product->creator?->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst($product->creator?->role ?? '-') }}</div>
                                <div class="text-xs text-gray-400 mt-1">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    {{ $product->created_at?->format('d/m/Y H:i') ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1.5"></i> Pending
                                    </span>
                                @elseif($product->status === 'approved')
                                    <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1.5"></i> Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1.5"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Detail Button -->
                                    <button onclick="openDetailModal({{ json_encode($product) }})"
                                            class="inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded-lg shadow-sm transition duration-150"
                                            title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    @if($product->status === 'pending')
                                        <!-- Approve Button -->
                                        <button onclick="openApproveModal({{ $product->id }}, '{{ $product->name }}')"
                                                class="inline-flex items-center px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded-lg shadow-sm transition duration-150"
                                                title="Setujui">
                                            <i class="fas fa-check"></i>
                                        </button>

                                        <!-- Reject Button -->
                                        <button onclick="openRejectModal({{ $product->id }}, '{{ $product->name }}')"
                                                class="inline-flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-lg shadow-sm transition duration-150"
                                                title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $products->links() }}
        </div>
    @else
        <div class="p-12 text-center">
            <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
            <p class="text-gray-600 text-lg">Tidak ada pengajuan produk</p>
        </div>
    @endif
</div>

<!-- Detail Modal -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-semibold text-gray-800">Detail Produk</h3>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div id="detailContent" class="p-6">
            <!-- Content will be inserted by JavaScript -->
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Setujui Produk</h3>
        </div>

        <form id="approveForm" method="POST">
            @csrf
            <div class="p-6">
                <p class="text-gray-700 mb-4">Apakah Anda yakin ingin menyetujui produk ini?</p>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-sm text-gray-700">
                        <i class="fas fa-box text-green-600 mr-2"></i>
                        <span id="approveProductName" class="font-medium"></span>
                    </p>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-2">
                <button type="button" onclick="closeApproveModal()"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                    <i class="fas fa-check mr-2"></i>
                    Setujui
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Tolak Produk</h3>
        </div>

        <form id="rejectForm" method="POST">
            @csrf
            <div class="p-6">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-gray-700">
                        <i class="fas fa-box text-red-600 mr-2"></i>
                        <span id="rejectProductName" class="font-medium"></span>
                    </p>
                </div>

                <div>
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea id="rejection_reason" name="rejection_reason" rows="4" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Jelaskan alasan penolakan produk ini..."></textarea>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-2">
                <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                    <i class="fas fa-times mr-2"></i>
                    Tolak
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openDetailModal(product) {
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('detailContent');

    content.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                ${product.image ? `
                    <img src="/storage/${product.image}" alt="${product.name}" class="w-full rounded-lg shadow-md mb-4">
                ` : `
                    <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-image text-gray-400 text-6xl"></i>
                    </div>
                `}
            </div>

            <div>
                <h4 class="text-xl font-bold text-gray-800 mb-4">${product.name}</h4>

                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">SKU</p>
                        <p class="text-base font-medium text-gray-800">${product.sku}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Kategori</p>
                        <p class="text-base font-medium text-gray-800">${product.category.name}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Supplier</p>
                        <p class="text-base font-medium text-gray-800">${product.supplier.name}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Harga Beli</p>
                        <p class="text-base font-medium text-gray-800">Rp ${new Intl.NumberFormat('id-ID').format(product.purchase_price)}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Harga Jual</p>
                        <p class="text-base font-medium text-green-600">Rp ${new Intl.NumberFormat('id-ID').format(product.selling_price)}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Stok Minimum</p>
                        <p class="text-base font-medium text-gray-800">${product.minimum_stock} unit</p>
                    </div>

                    ${product.description ? `
                        <div>
                            <p class="text-sm text-gray-500">Deskripsi</p>
                            <p class="text-base text-gray-800">${product.description}</p>
                        </div>
                    ` : ''}

                    <div class="pt-3 border-t border-gray-200">
                        <p class="text-sm text-gray-500">Diajukan oleh</p>
                        <p class="text-base font-medium text-gray-800">${product.creator.name} (${product.creator.role})</p>
                        <p class="text-xs text-gray-500">${new Date(product.created_at).toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}</p>
                    </div>
                </div>
            </div>
        </div>
    `;

    modal.classList.remove('hidden');
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
}

function openApproveModal(productId, productName) {
    const modal = document.getElementById('approveModal');
    const form = document.getElementById('approveForm');
    const nameSpan = document.getElementById('approveProductName');

    form.action = `/admin/products/${productId}/approve`;
    nameSpan.textContent = productName;

    modal.classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}

function openRejectModal(productId, productName) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    const nameSpan = document.getElementById('rejectProductName');

    form.action = `/admin/products/${productId}/reject`;
    nameSpan.textContent = productName;

    modal.classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejection_reason').value = '';
}

// Close modal when clicking outside
document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) closeDetailModal();
});

document.getElementById('approveModal').addEventListener('click', function(e) {
    if (e.target === this) closeApproveModal();
});

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});
</script>
@endpush
