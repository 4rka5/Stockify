<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ $appName ?? 'Stockify' }}</title>
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

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Masuk ke Akun</h2>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center text-red-800">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span class="font-medium">{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="mb-4">
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

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="••••••••">
                    </div>
                    <div>
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline mt-2 inline-block">
                            Lupa password?
                        </a>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-2.5 rounded-lg font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Masuk
                </button>
                <button type="button" onclick="window.location='{{ route('register') }}'" class="w-full bg-gradient-to-r from-green-600 to-green-600 text-white py-2.5 rounded-lg font-medium hover:from-green-700 hover:to-green-700 transition-all duration-200 shadow-lg hover:shadow-xl mt-4">
                    <i class="fas fa-user-plus mr-2"></i>
                    Register
                </button>
            </form>
        </div>

        <!-- Demo Credentials -->
        <div class="mt-6 bg-white rounded-xl shadow-md p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                Demo Credentials
            </h3>
            <div class="space-y-2 text-xs text-gray-600">
                <div class="flex justify-between p-2 bg-gray-50 rounded">
                    <span><strong>Admin:</strong> admin@stockify.com</span>
                    <span class="text-gray-500">password</span>
                </div>
                <div class="flex justify-between p-2 bg-gray-50 rounded">
                    <span><strong>Manajer:</strong> manajer@stockify.com</span>
                    <span class="text-gray-500">password</span>
                </div>
                <div class="flex justify-between p-2 bg-gray-50 rounded">
                    <span><strong>Staff:</strong> staff1@stockify.com</span>
                    <span class="text-gray-500">password</span>
                </div>
                <div>
                    <span class="text-gray-500 italic">* Gunakan kredensial di atas untuk masuk dan mengeksplorasi fitur Stockify.</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-sm text-gray-600 pb-4">
            <p>&copy; {{ date('Y') }} Stockify. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
