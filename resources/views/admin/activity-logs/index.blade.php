@extends('layouts.admin')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')
@section('breadcrumb', 'Home / Activity Logs')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Riwayat Aktivitas Pengguna</h3>
            <p class="text-sm text-gray-600">Tracking semua aktivitas pengguna dalam sistem</p>
        </div>

        <!-- Cleanup Button -->
        <button onclick="document.getElementById('cleanupModal').classList.remove('hidden')"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
            <i class="fas fa-trash-alt mr-2"></i>
            Bersihkan Log Lama
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Aktivitas</p>
                <h4 class="text-2xl font-bold text-gray-800">{{ number_format($totalActivities) }}</h4>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-history text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Create Actions</p>
                <h4 class="text-2xl font-bold text-gray-800">{{ $actionCounts['create'] ?? 0 }}</h4>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-plus text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Update Actions</p>
                <h4 class="text-2xl font-bold text-gray-800">{{ $actionCounts['update'] ?? 0 }}</h4>
            </div>
            <div class="bg-yellow-100 p-3 rounded-full">
                <i class="fas fa-edit text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Delete Actions</p>
                <h4 class="text-2xl font-bold text-gray-800">{{ $actionCounts['delete'] ?? 0 }}</h4>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <i class="fas fa-trash text-red-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Top Active Users -->
@if($topUsers->count() > 0)
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h4 class="text-md font-semibold text-gray-800 mb-4">
        <i class="fas fa-users mr-2"></i>Pengguna Paling Aktif
    </h4>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        @foreach($topUsers as $topUser)
        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
            <div class="bg-blue-100 p-2 rounded-full">
                <i class="fas fa-user text-blue-600"></i>
            </div>
            <div>
                <p class="font-semibold text-sm">{{ $topUser->user->name ?? 'Unknown' }}</p>
                <p class="text-xs text-gray-600">{{ $topUser->count }} aktivitas</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Filters -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date', $filters['start_date']->format('Y-m-d')) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ request('end_date', $filters['end_date']->format('Y-m-d')) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- User Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pengguna</label>
                <select name="user_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Pengguna</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->role }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Action Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Aksi</label>
                <select name="action" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Aksi</option>
                    <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update</option>
                    <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                    <option value="approve" {{ request('action') == 'approve' ? 'selected' : '' }}>Approve</option>
                    <option value="reject" {{ request('action') == 'reject' ? 'selected' : '' }}>Reject</option>
                </select>
            </div>

            <!-- Model Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                <select name="model" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Model</option>
                    <option value="Product" {{ request('model') == 'Product' ? 'selected' : '' }}>Product</option>
                    <option value="Category" {{ request('model') == 'Category' ? 'selected' : '' }}>Category</option>
                    <option value="Supplier" {{ request('model') == 'Supplier' ? 'selected' : '' }}>Supplier</option>
                    <option value="User" {{ request('model') == 'User' ? 'selected' : '' }}>User</option>
                    <option value="StockTransaction" {{ request('model') == 'StockTransaction' ? 'selected' : '' }}>Stock Transaction</option>
                    <option value="StockOpname" {{ request('model') == 'StockOpname' ? 'selected' : '' }}>Stock Opname</option>
                </select>
            </div>
        </div>

        <!-- Search -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cari Deskripsi</label>
            <div class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari dalam deskripsi aktivitas..."
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.activity-logs.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Activity Logs Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($activities as $activity)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div>{{ $activity->created_at->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $activity->created_at->format('H:i:s') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($activity->user)
                            <div class="text-sm font-medium text-gray-900">{{ $activity->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $activity->user->role }}</div>
                        @else
                            <span class="text-xs text-gray-500">System</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $actionColors = [
                                'create' => 'bg-green-100 text-green-800',
                                'update' => 'bg-yellow-100 text-yellow-800',
                                'delete' => 'bg-red-100 text-red-800',
                                'login' => 'bg-blue-100 text-blue-800',
                                'logout' => 'bg-gray-100 text-gray-800',
                                'approve' => 'bg-emerald-100 text-emerald-800',
                                'reject' => 'bg-orange-100 text-orange-800',
                            ];
                            $colorClass = $actionColors[$activity->action] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $colorClass }}">
                            {{ ucfirst($activity->action) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $activity->model ?? '-' }}
                        @if($activity->model_id)
                            <span class="text-xs text-gray-500">#{{ $activity->model_id }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 max-w-md">
                        <div class="truncate" title="{{ $activity->description }}">
                            {{ $activity->description }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $activity->ip_address ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.activity-logs.show', $activity->id) }}"
                           class="inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-lg transition" title="Lihat Detail">
                            <i class="fas fa-eye mr-1"></i>
                            Lihat
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Tidak ada data aktivitas</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($activities->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $activities->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Cleanup Modal -->
<div id="cleanupModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-trash-alt mr-2 text-red-600"></i>
            Bersihkan Log Lama
        </h3>
        <form method="POST" action="{{ route('admin.activity-logs.cleanup') }}">
            @csrf
            <p class="text-sm text-gray-600 mb-4">
                Hapus semua log aktivitas yang lebih lama dari:
            </p>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Hari</label>
                <input type="number" name="days" value="90" min="1" max="365"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Contoh: 90 hari akan menghapus log lebih dari 3 bulan</p>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                    Hapus
                </button>
                <button type="button" onclick="document.getElementById('cleanupModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
