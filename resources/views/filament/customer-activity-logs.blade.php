{{-- resources/views/filament/tables/customer-activity-logs.blade.php --}}

@php use Carbon\Carbon; use App\Models\BackupUser; @endphp

<div class="space-y-3 py-2">

    @forelse($logs as $log)

        @php
            $getRecord   = fn() => $log;
            $userName    = BackupUser::find($log->user_id)?->name ?? 'System';
            $actionColor = match($log->action) {
                'created' => 'bg-green-100 text-green-700',
                'updated' => 'bg-yellow-100 text-yellow-700',
                'deleted' => 'bg-red-100 text-red-700',
                default   => 'bg-gray-100 text-gray-600',
            };
        @endphp

        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden text-sm">

            {{-- Header row --}}
            <div class="flex items-center justify-between px-4 py-2 bg-gray-50 dark:bg-gray-800">
                <div class="flex items-center gap-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold capitalize {{ $actionColor }}">
                        {{ $log->action }}
                    </span>
                    <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $userName }}</span>
                    @if($log->description)
                        <span class="text-gray-400 text-xs">— {{ $log->description }}</span>
                    @endif
                </div>
                <div class="text-xs text-gray-400 flex items-center gap-3">
                    <span>{{ $log->ip_address }}</span>
                    <span>{{ Carbon::parse($log->created_at)->format('d M Y, H:i') }}</span>
                </div>
            </div>

            {{-- Reuse your existing diff view --}}
            <div class="px-4 py-2">
                @include('filament.tables.activity-log-changes')
            </div>

        </div>

    @empty
        <div class="text-center py-8 text-gray-400 text-sm">No activity logs found for this customer.</div>
    @endforelse

</div>
