@extends('layouts.staff')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('breadcrumb', 'Home / Profil')

@section('content')
<div class="space-y-6">
    <!-- Profile Information Card -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="bg-purple-100 rounded-full p-3 mr-4">
                    <i class="fas fa-user text-purple-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Informasi Profil</h2>
                    <p class="text-sm text-gray-600">Data profil Anda (hanya bisa dilihat)</p>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-4">
            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Lengkap
                </label>
                <div class="px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-700">
                    {{ $user->name }}
                </div>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Email
                </label>
                <div class="px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-700">
                    {{ $user->email }}
                </div>
            </div>

            <!-- Role -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Role
                </label>
                <div class="px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-700">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-800">
                        <i class="fas fa-user-tag mr-2"></i>
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Information Card -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-full p-3 mr-4">
                    <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Informasi Akun</h2>
                    <p class="text-sm text-gray-600">Detail tambahan tentang akun Anda</p>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-4">
            <!-- User ID -->
            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                <span class="text-gray-600 font-medium">User ID</span>
                <span class="text-gray-800">#{{ $user->id }}</span>
            </div>

            <!-- Created At -->
            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                <span class="text-gray-600 font-medium">Akun Dibuat</span>
                <span class="text-gray-800">{{ $user->created_at->format('d F Y') }}</span>
            </div>

            <!-- Last Updated -->
            <div class="flex justify-between items-center py-3">
                <span class="text-gray-600 font-medium">Terakhir Diperbarui</span>
                <span class="text-gray-800">{{ $user->updated_at->format('d F Y, H:i') }}</span>
            </div>
        </div>
    </div>

    <!-- Information Notice -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-yellow-600 text-lg mr-3 mt-0.5"></i>
            <div>
                <h3 class="font-semibold text-yellow-800 mb-1">Informasi</h3>
                <p class="text-sm text-yellow-700">
                    Jika Anda perlu mengubah informasi profil Anda, silakan hubungi Administrator.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
