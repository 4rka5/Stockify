@extends(auth()->user()->isAdmin() ? 'layouts.admin' : (auth()->user()->isManajer() ? 'layouts.manajer' : 'layouts.staff'))

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')
@section('breadcrumb', 'Home / Notifikasi')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-bell mr-2"></i>
                Semua Notifikasi
                @if($unreadCount > 0)
                    <span class="ml-2 px-2 py-1 bg-red-500 text-white text-xs rounded-full">{{ $unreadCount }}</span>
                @endif
            </h3>
            <div class="flex gap-2">
                <form action="{{ route('notifications.read-all') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-lg transition">
                        <i class="fas fa-check-double mr-1"></i>
                        Tandai Semua Dibaca
                    </button>
                </form>
                <form action="{{ route('notifications.destroy-all') }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua notifikasi?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition">
                        <i class="fas fa-trash mr-1"></i>
                        Hapus Semua
                    </button>
                </form>
            </div>
        </div>

        <div class="divide-y divide-gray-200">
            @forelse($notifications as $notification)
                <div class="{{ !$notification->is_read ? 'bg-blue-50' : 'bg-white' }} hover:bg-gray-50 transition">
                    <div class="p-6 flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center
                                @if($notification->type === 'info') bg-blue-100 text-blue-600
                                @elseif($notification->type === 'success') bg-green-100 text-green-600
                                @elseif($notification->type === 'warning') bg-yellow-100 text-yellow-600
                                @else bg-red-100 text-red-600
                                @endif">
                                <i class="fas fa-bell text-xl"></i>
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-base font-semibold text-gray-900">
                                        {{ $notification->title }}
                                        @if(!$notification->is_read)
                                            <span class="ml-2 px-2 py-1 bg-blue-500 text-white text-xs rounded-full">Baru</span>
                                        @endif
                                    </h4>
                                    <p class="mt-1 text-sm text-gray-700">{{ $notification->message }}</p>
                                    <p class="mt-2 text-xs text-gray-500">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                        <span class="mx-2">•</span>
                                        {{ $notification->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 ml-4">
                                    @if(!$notification->is_read && $notification->link)
                                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-sm rounded transition">
                                                <i class="fas fa-external-link-alt mr-1"></i>
                                                Lihat
                                            </button>
                                        </form>
                                    @elseif(!$notification->is_read)
                                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded transition">
                                                <i class="fas fa-check mr-1"></i>
                                                Tandai Dibaca
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded transition">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <i class="fas fa-bell-slash text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak Ada Notifikasi</h3>
                    <p class="text-gray-500">Anda belum memiliki notifikasi apapun</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
