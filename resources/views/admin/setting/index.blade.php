@extends('layouts.admin')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')
@section('breadcrumb', 'Home / Pengaturan')

@section('content')
<div class="space-y-6">
    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Application Settings Card -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="bg-indigo-100 rounded-full p-3 mr-4">
                    <i class="fas fa-palette text-indigo-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Pengaturan Aplikasi</h2>
                    <p class="text-sm text-gray-600">Atur nama dan logo aplikasi</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.app.update') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <!-- App Name -->
            <div class="mb-4">
                <label for="app_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Aplikasi
                </label>
                <input type="text" id="app_name" name="app_name" value="{{ old('app_name', $appName) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('app_name') border-red-500 @enderror">
                @error('app_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- App Logo -->
            <div class="mb-4">
                <label for="app_logo" class="block text-sm font-medium text-gray-700 mb-2">
                    Logo Aplikasi
                </label>

                @if($appLogo)
                    <div class="mb-3 flex items-center space-x-4">
                        <img src="{{ asset('storage/' . $appLogo) }}" alt="Current Logo" class="h-16 w-16 object-contain border border-gray-300 rounded-lg p-2">
                        <span class="text-sm text-gray-600">Logo saat ini</span>
                    </div>
                @endif

                <div class="flex items-center space-x-3">
                    <input type="file" id="app_logo" name="app_logo" accept="image/*" class="hidden" onchange="previewLogo(event)">
                    <label for="app_logo" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 cursor-pointer transition">
                        <i class="fas fa-upload mr-2"></i>
                        Pilih Logo
                    </label>
                    <span id="file-name" class="text-sm text-gray-600">Belum ada file dipilih</span>
                </div>
                @error('app_logo')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror

                <!-- Preview -->
                <div id="logo-preview" class="mt-3 hidden">
                    <p class="text-sm text-gray-600 mb-2">Preview:</p>
                    <img id="preview-image" src="" alt="Preview" class="h-16 w-16 object-contain border border-gray-300 rounded-lg p-2">
                </div>

                <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG, GIF, SVG. Maksimal 2MB.</p>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Pengaturan Aplikasi
                </button>
            </div>
        </form>
    </div>

    <!-- Profile Information Card -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-full p-3 mr-4">
                    <i class="fas fa-user text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Informasi Profil</h2>
                    <p class="text-sm text-gray-600">Perbarui informasi profil dan email Anda</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.profile.update') }}" class="p-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Lengkap
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email
                </label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role (Read Only) -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Role
                </label>
                <div class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                        <i class="fas fa-crown mr-2"></i>
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password Card -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="bg-yellow-100 rounded-full p-3 mr-4">
                    <i class="fas fa-lock text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Ubah Password</h2>
                    <p class="text-sm text-gray-600">Pastikan akun Anda menggunakan password yang kuat</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.password.update') }}" class="p-6">
            @csrf
            @method('PUT')

            <!-- Current Password -->
            <div class="mb-4">
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password Saat Ini
                </label>
                <input type="password" id="current_password" name="current_password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('current_password') border-red-500 @enderror">
                @error('current_password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password Baru
                </label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Konfirmasi Password Baru
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Password Requirements Info -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm font-medium text-blue-800 mb-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Persyaratan Password:
                </p>
                <ul class="text-sm text-blue-700 space-y-1 ml-5">
                    <li>• Minimal 8 karakter</li>
                    <li>• Kombinasi huruf dan angka lebih aman</li>
                </ul>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                    <i class="fas fa-key mr-2"></i>
                    Ubah Password
                </button>
            </div>
        </form>
    </div>

    <!-- Account Information Card -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="bg-purple-100 rounded-full p-3 mr-4">
                    <i class="fas fa-info-circle text-purple-600 text-xl"></i>
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
</div>

<script>
function previewLogo(event) {
    const file = event.target.files[0];
    const fileNameSpan = document.getElementById('file-name');

    if (file) {
        // Update file name display
        fileNameSpan.textContent = file.name;

        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-image').src = e.target.result;
            document.getElementById('logo-preview').classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    } else {
        fileNameSpan.textContent = 'Belum ada file dipilih';
        document.getElementById('logo-preview').classList.add('hidden');
    }
}
</script>
@endsection
