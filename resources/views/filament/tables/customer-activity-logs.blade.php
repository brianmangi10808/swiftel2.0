{{-- resources/views/filament/tables/customer-activity-logs.blade.php --}}
@php
    use Carbon\Carbon;

    $formatValue = function ($value, $key = null) {
        if ($value === null || $value === '') return '-';

        if ($key === 'status') {
            return in_array(strtolower((string)$value), ['online', 'open', 'active', '1'], true)
                ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Online</span>'
                : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Offline</span>';
        }

        $booleanFields = ['enable', 'allow_mac', 'active', 'is_active', 'is_enabled'];
        if (in_array($key, $booleanFields) || is_bool($value)) {
            $bool = is_bool($value) ? $value : in_array($value, [1, '1', true, 'true'], true);
            return $bool
                ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Enabled</span>'
                : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Disabled</span>';
        }

        if ($key === 'sector_id'  && is_numeric($value)) return \App\Models\Sector::find($value)?->name  ?? "Sector #{$value}";
        if ($key === 'service_id' && is_numeric($value)) return \App\Models\Service::find($value)?->name ?? "Service #{$value}";
        if ($key === 'group_id'   && is_numeric($value)) return \App\Models\Group::find($value)?->name   ?? "Group #{$value}";
        if ($key === 'premise_id' && is_numeric($value)) return \App\Models\Premise::find($value)?->name ?? "Premise #{$value}";

        if (is_string($value) && strtotime($value)) {
            try { return Carbon::parse($value)->format('d M Y, H:i'); } catch (\Exception $e) {}
        }

        return htmlspecialchars((string)$value);
    };

    $normalize = function ($val) {
        if (is_bool($val)) return $val ? 1 : 0;
        if (in_array($val, ['true', '1', 1], true)) return 1;
        if (in_array($val, ['false', '0', 0, null, ''], true)) return 0;
        if (is_string($val) && in_array(strtolower($val), ['online', 'open', 'active'], true))   return 1;
        if (is_string($val) && in_array(strtolower($val), ['offline', 'closed', 'inactive'], true)) return 0;
        return $val;
    };
@endphp

<div class="rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden">
    @if($logs->count() > 0)

        <div class="space-y-0 divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($logs as $log)
                @php
                    $userName = \App\Models\BackupUser::find($log->user_id)?->name ?? 'System';
                    $old = $log->data['old'] ?? [];
                    $new = $log->data['new'] ?? [];

                    $actionColors = [
                        'created' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                        'updated' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                        'deleted' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                    ];
                    $badgeClass = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-800';
                @endphp

                <div class="bg-white dark:bg-gray-900">

                    {{-- Row header --}}
                    <div class="flex items-center justify-between px-6 py-3 bg-gray-50 dark:bg-gray-800">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize {{ $badgeClass }}">
                                {{ $log->action }}
                            </span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $userName }}</span>
                            @if($log->description)
                                <span class="text-xs text-gray-500 dark:text-gray-400">— {{ $log->description }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                            <span>{{ $log->ip_address }}</span>
                            <span>{{ Carbon::parse($log->created_at)->format('d M Y, H:i') }}</span>
                        </div>
                    </div>

                    {{-- Changes diff --}}
                    @if($old && $new)
                        @php
                            $hasDiff = false;
                            foreach ($new as $k => $v) {
                                if ($k === 'updated_at') continue;
                                if ($normalize($old[$k] ?? null) != $normalize($v)) { $hasDiff = true; break; }
                            }
                        @endphp

                        @if($hasDiff)
                            <div class="overflow-x-auto">
                                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Field</th>
                                            <th class="px-6 py-2 text-left text-xs font-medium text-red-500 uppercase tracking-wider w-1/3">Before</th>
                                            <th class="px-6 py-2 text-left text-xs font-medium text-green-600 uppercase tracking-wider w-1/3">After</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                                        @foreach($new as $key => $newValue)
                                            @php
                                                if ($key === 'updated_at') continue;
                                                $oldValue = $old[$key] ?? null;
                                                if ($normalize($oldValue) == $normalize($newValue)) continue;
                                            @endphp
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                                <td class="px-6 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">
                                                    {{ ucwords(str_replace('_', ' ', $key)) }}
                                                </td>
                                                <td class="px-6 py-2 text-sm text-red-600 dark:text-red-400">
                                                    {!! $formatValue($oldValue, $key) !!}
                                                </td>
                                                <td class="px-6 py-2 text-sm text-green-700 dark:text-green-400 font-semibold">
                                                    {!! $formatValue($newValue, $key) !!}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @elseif($log->action === 'created')
                        <p class="px-6 py-2 text-xs text-green-600">Customer record created.</p>
                    @endif

                </div>
            @endforeach
        </div>

        <div class="bg-gray-50 dark:bg-gray-800 px-6 py-3 border-t border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                Showing <span class="font-medium">{{ $logs->count() }}</span> log(s)
            </p>
        </div>

    @else
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No activity logs</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No changes have been recorded for this customer.</p>
        </div>
    @endif
</div>
