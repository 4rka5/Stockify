<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Manajer Dashboard') - {{ $appName ?? 'Stockify' }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Flowbite CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />

    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-teal-600 to-teal-800 text-white flex-shrink-0">
            <div class="p-6">
                <div class="flex items-center space-x-2">
                    @if($appLogo)
                        <div class="bg-white rounded-lg p-2">
                            <img src="{{ asset('storage/' . $appLogo) }}" alt="{{ $appName ?? 'Stockify' }}" class="w-8 h-8 object-contain">
                        </div>
                    @else
                        <div class="bg-white rounded-lg p-2">
                            <i class="fas fa-boxes text-teal-600 text-xl"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-xl font-bold">{{ $appName ?? 'Stockify' }}</h1>
                        <p class="text-xs text-teal-200">Manajer Gudang</p>
                    </div>
                </div>
            </div>

                <!-- Navigation -->
                <nav class="mt-6" x-data="{ openMaster: {{ request()->routeIs('manajer.products*', 'manajer.suppliers*') ? 'true' : 'false' }}, openStock: {{ request()->routeIs('manajer.stock*', 'manajer.transactions*') ? 'true' : 'false' }} }">
                    <a href="{{ route('manajer.dashboard') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('manajer.dashboard') ? 'bg-teal-700 border-l-4 border-white' : 'hover:bg-teal-700' }} transition">
                        <i class="fas fa-home w-6"></i>
                        <span>Dashboard</span>
                    </a>

                    <!-- Master Data Dropdown -->
                    <div>
                        <button @click="openMaster = !openMaster" class="flex items-center justify-between w-full px-6 py-3 {{ request()->routeIs('manajer.products*', 'manajer.suppliers*') ? 'bg-teal-700 border-l-4 border-white' : 'hover:bg-teal-700' }} transition">
                            <div class="flex items-center">
                                <i class="fas fa-database w-6"></i>
                                <span>Master Data</span>
                            </div>
                            <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': openMaster }"></i>
                        </button>
                        <div x-show="openMaster" x-collapse class="bg-teal-900">
                            <a href="{{ route('manajer.products.index') }}" class="flex items-center px-6 py-2 pl-12 {{ request()->routeIs('manajer.products*') ? 'bg-teal-800' : 'hover:bg-teal-800' }} transition">
                                <i class="fas fa-box w-6 text-sm"></i>
                                <span>Produk</span>
                            </a>
                            <a href="{{ route('manajer.suppliers.index') }}" class="flex items-center px-6 py-2 pl-12 {{ request()->routeIs('manajer.suppliers*') ? 'bg-teal-800' : 'hover:bg-teal-800' }} transition">
                                <i class="fas fa-truck w-6 text-sm"></i>
                                <span>Supplier</span>
                            </a>
                        </div>
                    </div>

                    <!-- Stok & Transaksi Dropdown -->
                    <div>
                        <button @click="openStock = !openStock" class="flex items-center justify-between w-full px-6 py-3 {{ request()->routeIs('manajer.stock*', 'manajer.transactions*') ? 'bg-teal-700 border-l-4 border-white' : 'hover:bg-teal-700' }} transition">
                            <div class="flex items-center">
                                <i class="fas fa-warehouse w-6"></i>
                                <span>Stok & Transaksi</span>
                            </div>
                            <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': openStock }"></i>
                        </button>
                        <div x-show="openStock" x-collapse class="bg-teal-900">
                            <a href="{{ route('manajer.stock.index') }}" class="flex items-center px-6 py-2 pl-12 {{ request()->routeIs('manajer.stock.index') ? 'bg-teal-800' : 'hover:bg-teal-800' }} transition">
                                <i class="fas fa-boxes w-6 text-sm"></i>
                                <span>Monitor Stok</span>
                            </a>
                            <a href="{{ route('manajer.transactions.index') }}" class="flex items-center px-6 py-2 pl-12 {{ request()->routeIs('manajer.transactions*') ? 'bg-teal-800' : 'hover:bg-teal-800' }} transition">
                                <i class="fas fa-exchange-alt w-6 text-sm"></i>
                                <span>Riwayat Transaksi</span>
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('manajer.approval.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('manajer.approval*') ? 'bg-teal-700 border-l-4 border-white' : 'hover:bg-teal-700' }} transition">
                        <i class="fas fa-check-circle w-6"></i>
                        <span>Persetujuan</span>
                    </a>
                    <a href="{{ route('manajer.reports.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('manajer.reports*') ? 'bg-teal-700 border-l-4 border-white' : 'hover:bg-teal-700' }} transition">
                        <i class="fas fa-chart-line w-6"></i>
                        <span>Laporan</span>
                    </a>
                    <a href="{{ route('manajer.profile') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('manajer.profile*') ? 'bg-teal-700 border-l-4 border-white' : 'hover:bg-teal-700' }} transition">
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
            <!-- Top Navbar -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                        <p class="text-sm text-gray-600">@yield('breadcrumb', 'Home')</p>
                    </div>
                    <div class="flex items-center gap-3">
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
                                        <button type="submit" class="text-xs text-teal-600 hover:text-teal-800">Tandai Semua Dibaca</button>
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
                                    <a href="{{ route('notifications.index') }}" class="text-sm text-teal-600 hover:text-teal-800 font-medium">Lihat Semua</a>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="font-semibold text-gray-800">{{ Auth::user()->name ?? 'Manajer' }}</div>
                            <div class="text-sm text-gray-600">Manajer</div>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-teal-500 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr(Auth::user()->name ?? 'M', 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div id="alert-success" class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50" role="alert">
                        <i class="fas fa-check-circle flex-shrink-0 w-5 h-5"></i>
                        <div class="ml-3 text-sm font-medium">{{ session('success') }}</div>
                        <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#alert-success" aria-label="Close">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div id="alert-error" class="flex items-center p-4 mb-4 text-red-800 rounded-lg bg-red-50" role="alert">
                        <i class="fas fa-exclamation-circle flex-shrink-0 w-5 h-5"></i>
                        <div class="ml-3 text-sm font-medium">{{ session('error') }}</div>
                        <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#alert-error" aria-label="Close">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @if(session('warning'))
                    <div id="alert-warning" class="flex items-center p-4 mb-4 text-yellow-800 rounded-lg bg-yellow-50" role="alert">
                        <i class="fas fa-exclamation-triangle flex-shrink-0 w-5 h-5"></i>
                        <div class="ml-3 text-sm font-medium">{{ session('warning') }}</div>
                        <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-yellow-50 text-yellow-500 rounded-lg focus:ring-2 focus:ring-yellow-400 p-1.5 hover:bg-yellow-200 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#alert-warning" aria-label="Close">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Flowbite JS -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')
</body>
</html>
