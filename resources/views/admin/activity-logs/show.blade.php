@extends('layouts.admin')

@section('title', 'Detail Activity Log')
@section('page-title', 'Detail Activity Log')
@section('breadcrumb', 'Home / Activity Logs / Detail')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.activity-logs.index') }}" class="text-blue-600 hover:text-blue-700">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Activity Logs
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Aktivitas</h3>

            <div class="space-y-4">
                <div class="flex">
                    <span class="w-32 text-gray-600 font-medium">ID:</span>
                    <span class="text-gray-800">#{{ $activity->id }}</span>
                </div>

                <div class="flex">
                    <span class="w-32 text-gray-600 font-medium">Waktu:</span>
                    <span class="text-gray-800">{{ $activity->created_at->format('d F Y, H:i:s') }}</span>
                </div>

                <div class="flex">
                    <span class="w-32 text-gray-600 font-medium">Pengguna:</span>
                    <div>
                        @if($activity->user)
                            <div class="text-gray-800 font-medium">{{ $activity->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $activity->user->email }}</div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 inline-block mt-1">
                                {{ ucfirst($activity->user->role) }}
                            </span>
                        @else
                            <span class="text-gray-500">System</span>
                        @endif
                    </div>
                </div>

                <div class="flex">
                    <span class="w-32 text-gray-600 font-medium">Aksi:</span>
                    @php
                        $actionColors = [
                            'create' => 'bg-green-100 text-green-800',
                            'update' => 'bg-yellow-100 text-yellow-800',
                            'delete' => 'bg-red-100 text-red-800',
                            'login' => 'bg-blue-100 text-blue-800',
                            'logout' => 'bg-gray-100 text-gray-800',
                            'approve' => 'bg-emerald-100 text-emerald-800',
                            'reject' => 'bg-orange-100 text-orange-800',
                        ];
                        $colorClass = $actionColors[$activity->action] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $colorClass }}">
                        {{ ucfirst($activity->action) }}
                    </span>
                </div>

                @if($activity->model)
                <div class="flex">
                    <span class="w-32 text-gray-600 font-medium">Model:</span>
                    <span class="text-gray-800">{{ $activity->model }}</span>
                </div>
                @endif

                @if($activity->model_id)
                <div class="flex">
                    <span class="w-32 text-gray-600 font-medium">Model ID:</span>
                    <span class="text-gray-800">#{{ $activity->model_id }}</span>
                </div>
                @endif

                <div class="flex">
                    <span class="w-32 text-gray-600 font-medium">Deskripsi:</span>
                    <span class="text-gray-800">{{ $activity->description }}</span>
                </div>
            </div>
        </div>

        <!-- Properties (Old & New Values) -->
        @if($activity->properties)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Detail Perubahan</h3>

            @if(isset($activity->properties['old']) || isset($activity->properties['new']))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if(isset($activity->properties['old']))
                <div>
                    <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-history mr-2 text-red-600"></i>Nilai Lama
                    </h4>
                    <div class="bg-red-50 rounded-lg p-4">
                        <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ json_encode($activity->properties['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
                @endif

                @if(isset($activity->properties['new']))
                <div>
                    <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-check mr-2 text-green-600"></i>Nilai Baru
                    </h4>
                    <div class="bg-green-50 rounded-lg p-4">
                        <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ json_encode($activity->properties['new'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
                @endif
            </div>
            @elseif(isset($activity->properties['reason']))
            <div class="bg-yellow-50 rounded-lg p-4">
                <p class="text-sm text-gray-700"><strong>Alasan:</strong> {{ $activity->properties['reason'] }}</p>
            </div>
            @else
            <div class="bg-gray-50 rounded-lg p-4">
                <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ json_encode($activity->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Technical Info -->
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-info-circle mr-2"></i>Informasi Teknis
            </h3>

            <div class="space-y-3">
                <div>
                    <span class="text-sm text-gray-600 font-medium">IP Address:</span>
                    <p class="text-gray-800 font-mono text-sm mt-1">{{ $activity->ip_address ?? '-' }}</p>
                </div>

                <div>
                    <span class="text-sm text-gray-600 font-medium">User Agent:</span>
                    <p class="text-gray-800 text-xs mt-1 break-all">{{ $activity->user_agent ?? '-' }}</p>
                </div>

                <div>
                    <span class="text-sm text-gray-600 font-medium">Timestamp:</span>
                    <p class="text-gray-800 text-sm mt-1">{{ $activity->created_at->toIso8601String() }}</p>
                </div>

                <div>
                    <span class="text-sm text-gray-600 font-medium">Relative Time:</span>
                    <p class="text-gray-800 text-sm mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>

        <!-- Related Activities -->
        @if($activity->user)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-history mr-2"></i>Aktivitas Terkait
            </h3>

            <div class="space-y-2">
                @php
                    $relatedActivities = \App\Models\ActivityLog::where('user_id', $activity->user_id)
                        ->where('id', '!=', $activity->id)
                        ->latest()
                        ->limit(5)
                        ->get();
                @endphp

                @forelse($relatedActivities as $related)
                <div class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <a href="{{ route('admin.activity-logs.show', $related->id) }}" class="block">
                        <div class="flex justify-between items-start">
                            <span class="text-xs text-gray-500">{{ $related->created_at->format('d/m/y H:i') }}</span>
                            @php
                                $actionColors = [
                                    'create' => 'bg-green-100 text-green-800',
                                    'update' => 'bg-yellow-100 text-yellow-800',
                                    'delete' => 'bg-red-100 text-red-800',
                                    'login' => 'bg-blue-100 text-blue-800',
                                    'logout' => 'bg-gray-100 text-gray-800',
                                    'approve' => 'bg-emerald-100 text-emerald-800',
                                    'reject' => 'bg-orange-100 text-orange-800',
                                ];
                                $colorClass = $actionColors[$related->action] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $colorClass }}">
                                {{ ucfirst($related->action) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-800 mt-1 truncate">{{ $related->description }}</p>
                    </a>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">Tidak ada aktivitas terkait</p>
                @endforelse
            </div>

            @if($relatedActivities->count() > 0)
            <a href="{{ route('admin.activity-logs.index', ['user_id' => $activity->user_id]) }}"
               class="block text-center text-blue-600 hover:text-blue-700 text-sm mt-3">
                Lihat Semua Aktivitas User Ini
            </a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
