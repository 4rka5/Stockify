@extends('layouts.admin')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')
@section('breadcrumb', 'Home / User')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-lg font-semibold text-gray-800">Daftar User</h3>
        <p class="text-sm text-gray-600">Kelola semua user sistem</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
        <i class="fas fa-plus mr-2"></i>
        Tambah User
    </a>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Admin</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $users->where('role', 'admin')->count() }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-user-shield text-blue-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-teal-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Manajer</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $users->where('role', 'manajer gudang')->count() }}</p>
            </div>
            <div class="bg-teal-100 rounded-full p-3">
                <i class="fas fa-user-tie text-teal-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Staff</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $users->where('role', 'staff gudang')->count() }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-users text-purple-500 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-search mr-1"></i> Cari User
            </label>
            <input type="text" id="search" name="search" value="{{ $keyword ?? '' }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Cari berdasarkan nama atau email...">
        </div>
        <div class="md:w-64">
            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-filter mr-1"></i> Filter Role
            </label>
            <select id="role" name="role"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Role</option>
                <option value="admin" {{ ($role ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="manajer gudang" {{ ($role ?? '') === 'manajer gudang' ? 'selected' : '' }}>Manajer Gudang</option>
                <option value="staff gudang" {{ ($role ?? '') === 'staff gudang' ? 'selected' : '' }}>Staff Gudang</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-search mr-2"></i>
                Cari
            </button>
            <a href="{{ route('admin.users.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                <i class="fas fa-redo mr-2"></i>
                Reset
            </a>
        </div>
    </form>

    @if(isset($keyword) || isset($role))
        <div class="mt-3 flex items-center text-sm text-gray-600">
            <i class="fas fa-info-circle mr-2"></i>
            <span>Menampilkan hasil pencarian untuk:
                @if($keyword)
                    <span class="font-semibold">"{{ $keyword }}"</span>
                @endif
                @if($keyword && $role)
                    <span class="mx-1">•</span>
                @endif
                @if($role)
                    <span class="font-semibold">Role: {{ ucwords($role) }}</span>
                @endif
                <span class="ml-2">({{ $users->count() }} user ditemukan)</span>
            </span>
        </div>
    @endif
</div>

<!-- Users Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $index => $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full flex items-center justify-center text-white font-bold
                                        @if($user->role === 'admin') bg-blue-500
                                        @elseif($user->role === 'manajer gudang') bg-teal-500
                                        @else bg-purple-500
                                        @endif">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($user->role === 'admin') bg-blue-100 text-blue-800
                                @elseif($user->role === 'manajer gudang') bg-teal-100 text-teal-800
                                @else bg-purple-100 text-purple-800
                                @endif">
                                @if($user->role === 'admin')
                                    <i class="fas fa-user-shield mr-1"></i> Admin
                                @elseif($user->role === 'manajer gudang')
                                    <i class="fas fa-user-tie mr-1"></i> Manajer
                                @else
                                    <i class="fas fa-user mr-1"></i> Staff
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Aktif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-lg transition" title="Edit">
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit
                                </a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition" title="Hapus">
                                            <i class="fas fa-trash mr-1"></i>
                                            Hapus
                                        </button>
                                    </form>
                                @else
                                    <button disabled class="inline-flex items-center px-3 py-2 bg-gray-300 text-gray-500 text-sm rounded-lg cursor-not-allowed" title="Tidak dapat menghapus akun sendiri">
                                        <i class="fas fa-trash mr-1"></i>
                                        Hapus
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">Tidak ada data user</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
