<div>
    @php
        // Format values including dates, foreign keys, and booleans
        $formatValue = function ($value, $key = null) use ($getRecord) {
            if ($value === null || $value === '') return '-';

            // Handle status field specifically - show as Online/Offline or Open/Closed
            if ($key === 'status') {
                // Check if it's a string status
                if (in_array(strtolower($value), ['online', 'open', 'active', '1', 1, true], true)) {
                    return '<span class="text-green-600 font-semibold">Online</span>';
                } else {
                    return '<span class="text-red-600 font-semibold">Offline</span>';
                }
            }

            // Handle other boolean fields (1/0) - show as Enabled/Disabled
            $booleanFields = ['enable', 'allow_mac', 'active', 'is_active', 'is_enabled'];
            if (in_array($key, $booleanFields) || is_bool($value)) {
                $boolValue = is_bool($value) ? $value : in_array($value, [1, '1', true, 'true'], true);
                return $boolValue ? 
                    '<span class="text-green-600 font-semibold">Enabled</span>' : 
                    '<span class="text-red-600 font-semibold">Disabled</span>';
            }

            // Handle sector_id specifically
            if ($key === 'sector_id' && is_numeric($value)) {
                $sector = \App\Models\Sector::find($value);
                return $sector ? $sector->name : "Sector #{$value}";
            }

            // Handle service_id
            if ($key === 'service_id' && is_numeric($value)) {
                $service = \App\Models\Service::find($value);
                return $service ? $service->name : "Service #{$value}";
            }

            // Handle group_id
            if ($key === 'group_id' && is_numeric($value)) {
                $group = \App\Models\Group::find($value);
                return $group ? $group->name : "Group #{$value}";
            }

            // Handle premise_id
            if ($key === 'premise_id' && is_numeric($value)) {
                $premise = \App\Models\Premise::find($value);
                return $premise ? $premise->name : "Premise #{$value}";
            }

            // Date formatting
            if (strtotime($value)) {
                try {
                    return \Carbon\Carbon::parse($value)->format('d/m/y');
                } catch (\Exception $e) {
                    // Not a valid date
                }
            }

            return $value;
        };

        // Normalize values to compare properly
        $normalize = function ($val) {
            if (is_bool($val)) return $val ? 1 : 0;
            if (in_array($val, ['true', '1', 1], true)) return 1;
            if (in_array($val, ['false', '0', 0], true)) return 0;
            // Normalize status values
            if (in_array(strtolower($val), ['online', 'open', 'active'], true)) return 1;
            if (in_array(strtolower($val), ['offline', 'closed', 'inactive'], true)) return 0;
            return $val;
        };

        // Make field names more readable
        $formatFieldName = function ($key) {
            return ucwords(str_replace('_', ' ', $key));
        };

        $old = $getRecord()->data['old'] ?? [];
        $new = $getRecord()->data['new'] ?? [];
    @endphp

    @if($old && $new)
        <table class="w-full text-sm border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-2 py-1 text-left">Field</th>
                    <th class="border px-2 py-1 text-left">Old</th>
                    <th class="border px-2 py-1 text-left">New</th>
                </tr>
            </thead>
            <tbody>

                @foreach($new as $key => $newValue)

                    @php
                        if ($key === 'updated_at') continue;

                        $oldValue = $old[$key] ?? null;

                        // Skip if values are the same after normalization
                        if ($normalize($oldValue) == $normalize($newValue)) continue;
                    @endphp

                    <tr>
                        <td class="border px-2 py-1 font-medium">{{ $formatFieldName($key) }}</td>
                        <td class="border px-2 py-1">{!! $formatValue($oldValue, $key) !!}</td>
                        <td class="border px-2 py-1">{!! $formatValue($newValue, $key) !!}</td>
                    </tr>

                @endforeach

            </tbody>
        </table>
    @else
        <p class="text-gray-500">No change data available.</p>
    @endif
</div>