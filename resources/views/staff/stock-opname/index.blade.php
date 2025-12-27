@extends('layouts.staff')

@section('title', 'Stock Opname')
@section('page-title', 'Stock Opname - Pengecekan Fisik')
@section('breadcrumb', 'Home / Stock Opname')

@section('content')
<div class="mb-6">
    <h3 class="text-lg font-semibold text-gray-800">Stock Opname - Pengecekan Fisik</h3>
    <p class="text-sm text-gray-600">Input hasil pengecekan stok fisik di gudang</p>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
    <div class="flex items-center">
        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
        <div>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
    <div class="flex items-center">
        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
        <div>
            <p class="text-red-800 font-medium">{{ session('error') }}</p>
        </div>
    </div>
</div>
@endif

@if(session('info'))
<div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
    <div class="flex items-center">
        <i class="fas fa-info-circle text-blue-500 text-xl mr-3"></i>
        <div>
            <p class="text-blue-800 font-medium">{{ session('info') }}</p>
        </div>
    </div>
</div>
@endif

<!-- Info Card -->
<div class="bg-purple-50 border-l-4 border-purple-500 p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-purple-500 text-xl"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-purple-800">Petunjuk Penggunaan</h3>
            <div class="mt-2 text-sm text-purple-700">
                <ul class="list-disc list-inside space-y-1">
                    <li>Pilih produk yang akan dicek dengan centang checkbox</li>
                    <li>Masukkan jumlah stok fisik hasil pengecekan langsung di gudang</li>
                    <li>Sistem akan otomatis menghitung selisih dengan stok di sistem</li>
                    <li>Semua produk yang dicentang akan dikirim ke manajer untuk disetujui</li>
                    <li>Tambahkan catatan jika diperlukan (misalnya: produk rusak, hilang, dll)</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Recent Opnames -->
@if($recentOpnames->count() > 0)
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <h3 class="text-md font-semibold text-gray-800 mb-3">
        <i class="fas fa-history text-purple-500 mr-2"></i>
        Pengecekan Terakhir Anda
    </h3>
    <div class="space-y-2">
        @foreach($recentOpnames as $opname)
            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ $opname->product->name }}</p>
                    <p class="text-xs text-gray-500">
                        {{ $opname->checked_at->format('d/m/Y H:i') }} |
                        Sistem: {{ $opname->system_stock }} → Fisik: {{ $opname->physical_stock }}
                    </p>
                </div>
                <span class="px-2 py-1 text-xs rounded {{ $opname->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($opname->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                    {{ ucfirst($opname->status) }}
                </span>
            </div>
        @endforeach
    </div>
    <div class="mt-3 text-center">
        <a href="{{ route('staff.stock-opname.history') }}" class="text-sm text-purple-600 hover:text-purple-800">
            Lihat Semua Riwayat →
        </a>
    </div>
</div>
@endif

<!-- Assigned Tasks -->
@if($assignedTasks->count() > 0)
<div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-tasks text-yellow-500 text-xl"></i>
        </div>
        <div class="ml-3 flex-1">
            <h3 class="text-sm font-medium text-yellow-800 mb-3">Tugas Cek Stok dari Manajer</h3>
            <div class="space-y-2">
                @foreach($assignedTasks as $task)
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">{{ $task->product->name }}</p>
                                <p class="text-xs text-gray-600 mt-1">
                                    <i class="fas fa-user text-yellow-500 mr-1"></i>
                                    Dari: {{ $task->assignedBy->name ?? 'Manajer' }} |
                                    <i class="fas fa-clock text-yellow-500 mr-1"></i>
                                    {{ $task->assigned_at->format('d/m/Y H:i') }}
                                </p>
                                @if($task->notes)
                                    <p class="text-xs text-gray-500 mt-1 italic">
                                        <i class="fas fa-sticky-note mr-1"></i>{{ $task->notes }}
                                    </p>
                                @endif
                            </div>
                            <button onclick="scrollToProduct({{ $task->product_id }})"
                                    class="ml-3 px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white text-xs rounded-lg transition">
                                <i class="fas fa-arrow-down mr-1"></i>
                                Cek Sekarang
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<!-- Search & Filter -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form action="{{ route('staff.stock-opname.index') }}" method="GET" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari produk..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>
        <div class="w-48">
            <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">
            <i class="fas fa-search mr-2"></i>Filter
        </button>
        <a href="{{ route('staff.stock-opname.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">
            <i class="fas fa-redo"></i>
        </a>
    </form>
</div>

<!-- Stock Opname Form -->
<form action="{{ route('staff.stock-opname.store') }}" method="POST" id="opnameForm">
    @csrf
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="selectAll" class="rounded">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">SKU</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stok Sistem</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stok Fisik</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Selisih</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $index => $product)
                        <tr class="hover:bg-gray-50" data-index="{{ $index }}">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="selected[]" value="{{ $product->id }}"
                                       class="product-checkbox rounded" data-product-id="{{ $product->id }}">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                        <div class="text-gray-500">{{ $product->category->name ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">
                                {{ $product->sku }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="system-stock text-lg font-bold text-gray-800" data-stock="{{ $product->current_stock }}">
                                    {{ $product->current_stock }}
                                    <div class="text-xs text-gray-500 font-normal">unit</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <input type="hidden" name="opname_data[{{ $product->id }}][product_id]" value="{{ $product->id }}" disabled>
                                <input type="number"
                                       name="opname_data[{{ $product->id }}][physical_stock]"
                                       class="physical-stock w-24 px-3 py-2 border rounded-lg text-center font-semibold"
                                       min="0" placeholder="0"
                                       data-product-id="{{ $product->id }}" disabled>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="difference-display px-3 py-1 text-sm font-bold rounded-full" data-product-id="{{ $product->id }}">-</span>
                            </td>
                            <td class="px-6 py-4">
                                <input type="text"
                                       name="opname_data[{{ $product->id }}][notes]"
                                       class="w-full px-3 py-2 border rounded-lg text-sm"
                                       placeholder="Catatan..." disabled>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <i class="fas fa-box-open text-gray-300 text-5xl mb-4"></i>
                                <p class="text-gray-500">Tidak ada produk</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($products->count() > 0)
    <div class="mt-6 flex justify-between items-center">
        <div class="text-sm text-gray-600">
            {{ $products->links() }}
        </div>
        <div class="flex gap-3">
            <button type="button" onclick="resetForm()" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">
                <i class="fas fa-redo mr-2"></i>Reset
            </button>
            <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">
                <i class="fas fa-save mr-2"></i>Kirim ke Manajer
            </button>
        </div>
    </div>
    @endif
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');

    // Select All
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                toggleInputs(checkbox);
            });
        });
    }

    // Individual checkbox
    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            toggleInputs(this);
            updateSelectAll();
        });
    });

    // Physical stock input - Using event delegation
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('physical-stock')) {
            const productId = e.target.dataset.productId;
            if (productId) {
                calculateDifference(productId);
            }
        }
    });

    function toggleInputs(checkbox) {
        const row = checkbox.closest('tr');
        const inputs = row.querySelectorAll('input[type="number"], input[type="text"], input[type="hidden"]');

        inputs.forEach(input => {
            input.disabled = !checkbox.checked;
        });

        if (!checkbox.checked) {
            const physicalInput = row.querySelector('.physical-stock');
            physicalInput.value = '';
            const productId = checkbox.dataset.productId;
            const diffElement = row.querySelector(`.difference-display[data-product-id="${productId}"]`);
            if (diffElement) {
                diffElement.textContent = '-';
                diffElement.className = 'difference-display px-3 py-1 text-sm font-bold rounded-full';
            }
        } else {
            // Trigger calculation if there's already a value
            const physicalInput = row.querySelector('.physical-stock');
            if (physicalInput.value) {
                const productId = checkbox.dataset.productId;
                calculateDifference(productId);
            }
        }
    }

    function calculateDifference(productId) {
        const physicalStockInput = document.querySelector(`input.physical-stock[data-product-id="${productId}"]`);
        if (!physicalStockInput) {
            return;
        }

        const row = physicalStockInput.closest('tr');
        const systemStockDiv = row.querySelector('.system-stock');
        const systemStock = parseInt(systemStockDiv.dataset.stock);

        const inputValue = physicalStockInput.value.trim();
        const diffElement = row.querySelector(`.difference-display[data-product-id="${productId}"]`);

        if (!diffElement) {
            return;
        }

        if (inputValue === '' || inputValue === null) {
            diffElement.textContent = '-';
            diffElement.className = 'difference-display px-3 py-1 text-sm font-bold rounded-full';
            diffElement.removeAttribute('title');
        } else {
            const physicalStock = parseInt(inputValue);

            // Validasi jika input bukan angka valid
            if (isNaN(physicalStock)) {
                diffElement.textContent = '-';
                diffElement.className = 'difference-display px-3 py-1 text-sm font-bold rounded-full';
                diffElement.removeAttribute('title');
                return;
            }

            const difference = physicalStock - systemStock;

            // Format: Fisik - Sistem = Selisih
            diffElement.textContent = difference > 0 ? `+${difference}` : difference;

            if (difference > 0) {
                diffElement.className = 'difference-display px-3 py-1 text-sm font-bold rounded-full bg-green-100 text-green-800';
                diffElement.title = `Kelebihan ${difference} unit dari sistem`;
            } else if (difference < 0) {
                diffElement.className = 'difference-display px-3 py-1 text-sm font-bold rounded-full bg-red-100 text-red-800';
                diffElement.title = `Kekurangan ${Math.abs(difference)} unit dari sistem`;
            } else {
                diffElement.className = 'difference-display px-3 py-1 text-sm font-bold rounded-full bg-gray-100 text-gray-800';
                diffElement.title = 'Stok sesuai dengan sistem';
            }
        }
    }

    function updateSelectAll() {
        const allChecked = Array.from(productCheckboxes).every(cb => cb.checked);
        const someChecked = Array.from(productCheckboxes).some(cb => cb.checked);

        if (selectAll) {
            selectAll.checked = allChecked;
            selectAll.indeterminate = someChecked && !allChecked;
        }
    }

    function resetForm() {
        productCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
            toggleInputs(checkbox);
        });
        if (selectAll) selectAll.checked = false;
    }

    window.resetForm = resetForm;

    function scrollToProduct(productId) {
        const checkbox = document.querySelector(`input.product-checkbox[value="${productId}"]`);
        if (checkbox) {
            checkbox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            checkbox.checked = true;
            toggleInputs(checkbox);
            // Highlight row briefly
            const row = checkbox.closest('tr');
            row.classList.add('bg-yellow-100');
            setTimeout(() => row.classList.remove('bg-yellow-100'), 2000);
        }
    }

    window.scrollToProduct = scrollToProduct;

    // Auto-hide alerts after 5 seconds
    @if(session('success') || session('error') || session('info'))
    setTimeout(function() {
        const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50, .bg-blue-50');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
    @endif

    // Show success modal if data was submitted
    @if(session('success'))
    // Optional: You can add SweetAlert or custom modal here for better UX
    @endif
});
</script>
@endpush
