@extends(auth()->user()->isAdmin() ? 'layouts.admin' : (auth()->user()->isManajer() ? 'layouts.manajer' : 'layouts.staff'))

@section('title', 'Detail Notifikasi')
@section('page-title', 'Detail Notifikasi')
@section('breadcrumb', 'Home / Notifikasi / Detail')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('notifications.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800 transition">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Semua Notifikasi
        </a>
    </div>

    <!-- Notification Detail Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200
            @if($notification->type === 'info') bg-blue-50
            @elseif($notification->type === 'success') bg-green-50
            @elseif($notification->type === 'warning') bg-yellow-50
            @else bg-red-50
            @endif">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center
                        @if($notification->type === 'info') bg-blue-100 text-blue-600
                        @elseif($notification->type === 'success') bg-green-100 text-green-600
                        @elseif($notification->type === 'warning') bg-yellow-100 text-yellow-600
                        @else bg-red-100 text-red-600
                        @endif">
                        <i class="fas
                            @if($notification->type === 'info') fa-info-circle
                            @elseif($notification->type === 'success') fa-check-circle
                            @elseif($notification->type === 'warning') fa-exclamation-triangle
                            @else fa-times-circle
                            @endif text-3xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $notification->title }}</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-clock mr-1"></i>
                            {{ $notification->created_at->format('d F Y, H:i') }}
                            <span class="mx-2">•</span>
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($notification->is_read)
                        <span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full">
                            <i class="fas fa-check mr-1"></i> Sudah Dibaca
                        </span>
                    @else
                        <span class="px-3 py-1 bg-blue-500 text-white text-sm rounded-full">
                            <i class="fas fa-bell mr-1"></i> Baru
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-6 py-8">
            <div class="prose max-w-none">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-envelope-open-text mr-2 text-gray-600"></i>
                    Pesan
                </h3>
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <p class="text-gray-700 text-base leading-relaxed whitespace-pre-line">{{ $notification->message }}</p>
                </div>
            </div>

            <!-- Additional Info -->
            @if($notification->data)
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-info-circle mr-2 text-gray-600"></i>
                        Informasi Tambahan
                    </h3>
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @php
                                $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
                            @endphp
                            @if($data && is_array($data))
                                @foreach($data as $key => $value)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-600 mb-1">{{ ucwords(str_replace('_', ' ', $key)) }}</dt>
                                        <dd class="text-sm text-gray-900 font-semibold">{{ is_array($value) ? json_encode($value) : $value }}</dd>
                                    </div>
                                @endforeach
                            @endif
                        </dl>
                    </div>
                </div>
            @endif

            <!-- Type Badge -->
            <div class="mt-6 flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium text-gray-600">Tipe Notifikasi:</span>
                    <span class="ml-2 px-3 py-1 rounded-full text-sm font-semibold
                        @if($notification->type === 'info') bg-blue-100 text-blue-800
                        @elseif($notification->type === 'success') bg-green-100 text-green-800
                        @elseif($notification->type === 'warning') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($notification->type) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($notification->link)
                    <a href="{{ $notification->link }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Lihat Detail Terkait
                    </a>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Related Notifications (Optional) -->
    @php
        $relatedNotifications = App\Models\Notification::where('user_id', auth()->id())
            ->where('id', '!=', $notification->id)
            ->where('type', $notification->type)
            ->latest()
            ->limit(3)
            ->get();
    @endphp

    @if($relatedNotifications->count() > 0)
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-bell mr-2"></i>
            Notifikasi Terkait
        </h3>
        <div class="grid grid-cols-1 gap-4">
            @foreach($relatedNotifications as $related)
                <a href="{{ route('notifications.show', $related->id) }}" class="block bg-white rounded-lg shadow-md hover:shadow-lg transition p-4">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center
                                @if($related->type === 'info') bg-blue-100 text-blue-600
                                @elseif($related->type === 'success') bg-green-100 text-green-600
                                @elseif($related->type === 'warning') bg-yellow-100 text-yellow-600
                                @else bg-red-100 text-red-600
                                @endif">
                                <i class="fas fa-bell"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-base font-semibold text-gray-900">
                                {{ $related->title }}
                                @if(!$related->is_read)
                                    <span class="ml-2 px-2 py-1 bg-blue-500 text-white text-xs rounded-full">Baru</span>
                                @endif
                            </h4>
                            <p class="mt-1 text-sm text-gray-600 line-clamp-2">{{ $related->message }}</p>
                            <p class="mt-2 text-xs text-gray-500">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $related->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
