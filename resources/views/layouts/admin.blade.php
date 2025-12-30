<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - {{ $appName ?? 'Stockify' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-blue-600 to-blue-800 text-white flex-shrink-0">
            <div class="p-6">
                <div class="flex items-center space-x-2">
                    @if($appLogo)
                        <div class="bg-white rounded-lg p-2">
                            <img src="{{ asset('storage/' . $appLogo) }}" alt="{{ $appName ?? 'Stockify' }}" class="w-8 h-8 object-contain">
                        </div>
                    @else
                        <div class="bg-white rounded-lg p-2">
                            <i class="fas fa-boxes text-blue-600 text-xl"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-xl font-bold">{{ $appName ?? 'Stockify' }}</h1>
                        <p class="text-xs text-blue-200">Admin Panel</p>
                    </div>
                </div>
            </div>

            <nav class="mt-6">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-700 border-l-4 border-white' : 'hover:bg-blue-700' }} transition">
                    <i class="fas fa-home w-6"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Produk Menu with Dropdown -->
                <div class="relative" x-data="{ open: {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.attributes.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="flex items-center justify-between w-full px-6 py-3 hover:bg-blue-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-box w-6"></i>
                            <span>Produk</span>
                        </div>
                        <i class="fas fa-chevron-down text-sm transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-collapse class="bg-blue-800">
                        <a href="{{ route('admin.products.index') }}" class="flex items-center px-6 py-2 pl-12 text-sm {{ request()->routeIs('admin.products.index') || request()->routeIs('admin.products.create') || request()->routeIs('admin.products.edit') || request()->routeIs('admin.products.show') ? 'bg-blue-900 border-l-4 border-white' : 'hover:bg-blue-900' }} transition">
                            <i class="fas fa-list mr-2"></i> Daftar Produk
                        </a>
                        <a href="{{ route('admin.attributes.index') }}" class="flex items-center px-6 py-2 pl-12 text-sm {{ request()->routeIs('admin.attributes.*') ? 'bg-blue-900 border-l-4 border-white' : 'hover:bg-blue-900' }} transition">
                            <i class="fas fa-list-ul mr-2"></i> Atribut
                        </a>
                        <a href="{{ route('admin.products.approval') }}" class="flex items-center px-6 py-2 pl-12 text-sm {{ request()->routeIs('admin.products.approval') ? 'bg-blue-900 border-l-4 border-white' : 'hover:bg-blue-900' }} transition relative">
                            <i class="fas fa-check-circle mr-2"></i> Approval
                            @php
                                $pendingProductCount = \App\Models\Product::where('status', 'pending')->count();
                            @endphp
                            @if($pendingProductCount > 0)
                                <span class="ml-auto bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                    {{ $pendingProductCount }}
                                </span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Master Data Menu with Dropdown -->
                <div class="relative" x-data="{ open: {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.suppliers.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="flex items-center justify-between w-full px-6 py-3 hover:bg-blue-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-database w-6"></i>
                            <span>Master Data</span>
                        </div>
                        <i class="fas fa-chevron-down text-sm transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-collapse class="bg-blue-800">
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center px-6 py-2 pl-12 text-sm {{ request()->routeIs('admin.categories.*') ? 'bg-blue-900 border-l-4 border-white' : 'hover:bg-blue-900' }} transition">
                            <i class="fas fa-tags mr-2"></i> Kategori
                        </a>
                        <a href="{{ route('admin.suppliers.index') }}" class="flex items-center px-6 py-2 pl-12 text-sm {{ request()->routeIs('admin.suppliers.*') ? 'bg-blue-900 border-l-4 border-white' : 'hover:bg-blue-900' }} transition">
                            <i class="fas fa-truck mr-2"></i> Supplier
                        </a>
                    </div>
                </div>

                <!-- User Management -->
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.users.*') ? 'bg-blue-700 border-l-4 border-white' : 'hover:bg-blue-700' }} transition">
                    <i class="fas fa-users w-6"></i>
                    <span>User Management</span>
                </a>

                <!-- Manajemen Stok Menu with Dropdown -->
                <div class="relative" x-data="{ open: {{ request()->routeIs('admin.stock-transactions.*') || request()->routeIs('admin.transactions.*') || request()->routeIs('admin.stock.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="flex items-center justify-between w-full px-6 py-3 hover:bg-blue-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-warehouse w-6"></i>
                            <span>Manajemen Stok</span>
                        </div>
                        <i class="fas fa-chevron-down text-sm transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-collapse class="bg-blue-800">
                        <a href="{{ route('admin.stock-transactions.index') }}" class="flex items-center px-6 py-2 pl-12 text-sm {{ request()->routeIs('admin.stock-transactions.*') || request()->routeIs('admin.transactions.*') ? 'bg-blue-900 border-l-4 border-white' : 'hover:bg-blue-900' }} transition">
                            <i class="fas fa-exchange-alt mr-2"></i> Transaksi Stok
                        </a>
                        <a href="{{ route('admin.stock.index') }}" class="flex items-center px-6 py-2 pl-12 text-sm {{ request()->routeIs('admin.stock.*') && !request()->routeIs('admin.stock-transactions.*') ? 'bg-blue-900 border-l-4 border-white' : 'hover:bg-blue-900' }} transition">
                            <i class="fas fa-chart-line mr-2"></i> Laporan Stok
                        </a>
                    </div>
                </div>

                <!-- Laporan & Analisis Menu with Dropdown -->
                <div class="relative" x-data="{ open: {{ request()->routeIs('admin.reports.*') || request()->routeIs('admin.activity-logs.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="flex items-center justify-between w-full px-6 py-3 hover:bg-blue-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-chart-bar w-6"></i>
                            <span>Laporan & Analisis</span>
                        </div>
                        <i class="fas fa-chevron-down text-sm transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-collapse class="bg-blue-800">
                        <a href="{{ route('admin.reports.index') }}" class="flex items-center px-6 py-2 pl-12 text-sm {{ request()->routeIs('admin.reports.*') ? 'bg-blue-900 border-l-4 border-white' : 'hover:bg-blue-900' }} transition">
                            <i class="fas fa-file-chart-line mr-2"></i> Laporan Komprehensif
                        </a>
                        <a href="{{ route('admin.activity-logs.index') }}" class="flex items-center px-6 py-2 pl-12 text-sm {{ request()->routeIs('admin.activity-logs.*') ? 'bg-blue-900 border-l-4 border-white' : 'hover:bg-blue-900' }} transition">
                            <i class="fas fa-history mr-2"></i> Activity Logs
                        </a>
                    </div>
                </div>

                <!-- Pengaturan -->
                <a href="{{ route('admin.settings.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.settings.*') ? 'bg-blue-700 border-l-4 border-white' : 'hover:bg-blue-700' }} transition">
                    <i class="fas fa-cog w-6"></i>
                    <span>Pengaturan</span>
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
                                        <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">Tandai Semua Dibaca</button>
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
                                        <form :action="'{{ url('/notifications') }}/' + notification.id + '/read'" method="POST" class="block border-b border-gray-100">
                                            @csrf
                                            <button type="submit" class="w-full text-left p-4 hover:bg-gray-50 transition">
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
                                    <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Semua</a>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-600">Administrator</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
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
