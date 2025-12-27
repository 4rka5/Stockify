<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Staff Panel') - {{ $appName ?? 'Stockify' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-purple-600 to-purple-800 text-white flex-shrink-0">
            <div class="p-6">
                <div class="flex items-center space-x-2">
                    @if($appLogo)
                        <div class="bg-white rounded-lg p-2">
                            <img src="{{ asset('storage/' . $appLogo) }}" alt="{{ $appName ?? 'Stockify' }}" class="w-8 h-8 object-contain">
                        </div>
                    @else
                        <div class="bg-white rounded-lg p-2">
                            <i class="fas fa-boxes text-purple-600 text-xl"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-xl font-bold">{{ $appName ?? 'Stockify' }}</h1>
                        <p class="text-xs text-purple-200">Staff Gudang</p>
                    </div>
                </div>
            </div>

            <nav class="mt-6">
                <a href="{{ route('staff.dashboard') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('staff.dashboard') ? 'bg-purple-700 border-l-4 border-white' : 'hover:bg-purple-700' }} transition">
                    <i class="fas fa-home w-6"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('staff.stock.in') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('staff.stock.in') ? 'bg-purple-700 border-l-4 border-white' : 'hover:bg-purple-700' }} transition">
                    <i class="fas fa-arrow-down w-6"></i>
                    <span>Barang Masuk</span>
                </a>
                <a href="{{ route('staff.stock.out') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('staff.stock.out') ? 'bg-purple-700 border-l-4 border-white' : 'hover:bg-purple-700' }} transition">
                    <i class="fas fa-arrow-up w-6"></i>
                    <span>Barang Keluar</span>
                </a>
                <a href="{{ route('staff.stock.check') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('staff.stock.check') ? 'bg-purple-700 border-l-4 border-white' : 'hover:bg-purple-700' }} transition">
                    <i class="fas fa-search w-6"></i>
                    <span>Cek Stok</span>
                </a>
                <a href="{{ route('staff.stock-opname.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('staff.stock-opname.*') ? 'bg-purple-700 border-l-4 border-white' : 'hover:bg-purple-700' }} transition">
                    <i class="fas fa-clipboard-check w-6"></i>
                    <span>Stock Opname</span>
                </a>
                <a href="{{ route('staff.products.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('staff.products.*') ? 'bg-purple-700 border-l-4 border-white' : 'hover:bg-purple-700' }} transition">
                    <i class="fas fa-box w-6"></i>
                    <span>Produk</span>
                </a>
                <a href="{{ route('staff.transactions.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('staff.transactions.*') ? 'bg-purple-700 border-l-4 border-white' : 'hover:bg-purple-700' }} transition">
                    <i class="fas fa-history w-6"></i>
                    <span>Riwayat</span>
                </a>
                <a href="{{ route('staff.notifications.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('staff.notifications.*') ? 'bg-purple-700 border-l-4 border-white' : 'hover:bg-purple-700' }} transition">
                    <i class="fas fa-bell w-6"></i>
                    <span>Notifikasi</span>
                    @if(isset($unreadNotifications) && $unreadNotifications > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1">{{ $unreadNotifications }}</span>
                    @endif
                </a>
                <a href="{{ route('staff.profile') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('staff.profile*') ? 'bg-purple-700 border-l-4 border-white' : 'hover:bg-purple-700' }} transition">
                    <i class="fas fa-user-circle w-6"></i>
                    <span>Profil Saya</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="flex items-center px-6 py-3 hover:bg-red-600 transition w-full text-left">
                        <i class="fas fa-sign-out-alt w-6"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                        <p class="text-sm text-gray-600">@yield('breadcrumb', 'Home')</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Notification Bell -->
                        <div class="relative" x-data="{ open: false, unreadCount: 0, notifications: [] }" x-init="
                            fetch('{{ route('notifications.get') }}')
                                .then(response => response.json())
                                .then(data => {
                                    unreadCount = data.unreadCount;
                                    notifications = data.notifications;
                                });
                            setInterval(() => {
                                fetch('{{ route('notifications.get') }}')
                                    .then(response => response.json())
                                    .then(data => {
                                        unreadCount = data.unreadCount;
                                        notifications = data.notifications;
                                    });
                            }, 30000);
                        ">
                            <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-full transition">
                                <i class="fas fa-bell text-xl"></i>
                                <span x-show="unreadCount > 0" x-text="unreadCount" class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"></span>
                            </button>

                            <!-- Notification Dropdown -->
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50" style="display: none;">
                                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                                    <h3 class="font-semibold text-gray-800">Notifikasi</h3>
                                    <form action="{{ route('notifications.read-all') }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs text-purple-600 hover:text-purple-800">Tandai Semua Dibaca</button>
                                    </form>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    <template x-if="notifications.length === 0">
                                        <div class="p-4 text-center text-gray-500">
                                            <i class="fas fa-bell-slash text-3xl mb-2"></i>
                                            <p class="text-sm">Tidak ada notifikasi</p>
                                        </div>
                                    </template>
                                    <template x-for="notification in notifications" :key="notification.id">
                                        <form :action="'{{ url('/notifications') }}/' + notification.id + '/read'" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full text-left p-4 hover:bg-gray-50 border-b border-gray-100 transition">
                                                <div class="flex items-start space-x-3">
                                                    <div :class="{
                                                        'bg-blue-100 text-blue-600': notification.type === 'info',
                                                        'bg-green-100 text-green-600': notification.type === 'success',
                                                        'bg-yellow-100 text-yellow-600': notification.type === 'warning',
                                                        'bg-red-100 text-red-600': notification.type === 'danger'
                                                    }" class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0">
                                                        <i class="fas fa-bell"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                                                        <p class="text-xs text-gray-600 mt-1" x-text="notification.message"></p>
                                                        <p class="text-xs text-gray-400 mt-1" x-text="new Date(notification.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit'})"></p>
                                                    </div>
                                                </div>
                                            </button>
                                        </form>
                                    </template>
                                </div>
                                <div class="p-3 border-t border-gray-200 text-center">
                                    <a href="{{ route('notifications.index') }}" class="text-sm text-purple-600 hover:text-purple-800 font-medium">Lihat Semua</a>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-600">Staff Gudang</p>
                        </div>
                        <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Alerts -->
            @if(session('success'))
                <div id="alert-success" class="flex items-center p-4 mx-6 mt-4 text-green-800 rounded-lg bg-green-50" role="alert">
                    <i class="fas fa-check-circle mr-3"></i>
                    <span class="sr-only">Success</span>
                    <div class="text-sm font-medium">{{ session('success') }}</div>
                    <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#alert-success" aria-label="Close">
                        <span class="sr-only">Close</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div id="alert-error" class="flex items-center p-4 mx-6 mt-4 text-red-800 rounded-lg bg-red-50" role="alert">
                    <i class="fas fa-exclamation-circle mr-3"></i>
                    <span class="sr-only">Error</span>
                    <div class="text-sm font-medium">{{ session('error') }}</div>
                    <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#alert-error" aria-label="Close">
                        <span class="sr-only">Close</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
</body>
</html>
