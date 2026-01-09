<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - {{ $appName ?? 'Stockify' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <!-- Logo & Title -->
        <div class="text-center mb-8 p-4">
            @if($appLogo)
                <div class="inline-flex items-center justify-center w-16 h-16 mb-4">
                    <img src="{{ asset('storage/' . $appLogo) }}" alt="{{ $appName ?? 'Stockify' }}" class="w-16 h-16 object-contain">
                </div>
            @else
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl mb-4 shadow-lg">
                    <i class="fas fa-boxes text-white text-2xl"></i>
                </div>
            @endif
            <h1 class="text-3xl font-bold text-gray-800">{{ $appName ?? 'Stockify' }}</h1>
            <p class="text-gray-600 mt-2">Sistem Manajemen Stok Barang</p>
        </div>

        <!-- Forgot Password Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                    <i class="fas fa-key text-blue-600 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 break-words">Lupa Password?</h2>
                <p class="text-gray-600 mt-2 text-sm break-words">Jangan khawatir, masukkan email Anda dan kami akan mengirimkan link untuk reset password.</p>
            </div>

            @if (session('status'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-start text-green-800">
                        <i class="fas fa-check-circle mr-2 mt-0.5 flex-shrink-0"></i>
                        <span class="font-medium break-words max-w-full" style="word-wrap: break-word; overflow-wrap: break-word;">{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-start text-red-800">
                        <i class="fas fa-exclamation-circle mr-2 mt-0.5 flex-shrink-0"></i>
                        <span class="font-medium break-words max-w-full" style="word-wrap: break-word; overflow-wrap: break-word;">{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="nama@email.com">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-2.5 rounded-lg font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Kirim Link Reset Password
                </button>

                <!-- Back to Login -->
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Kembali ke Login
                    </a>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-gray-600 text-sm pb-4">
            <p>&copy; 2025 Stockify. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
