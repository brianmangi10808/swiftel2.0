<div class="rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden">
    @if($authlogs->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Username
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            MAC
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            NAS
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Action
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Status
                        </th>
                        
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Session ID
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Reason
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Created At
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                    @foreach($authlogs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $log->username ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 font-mono">
                                {{ $log->mac ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ $log->nas ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                    {{ $log->action == 'login' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                                       ($log->action == 'logout' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : 
                                       'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300') }}">
                                    {{ $log->action ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                    {{ $log->status == 'success' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                       ($log->status == 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                       'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200') }}">
                                    {{ ucfirst($log->status ?? '-') }}
                                </span>
                            </td>
                           
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 font-mono text-xs">
                                {{ $log->session_id ? Str::limit($log->session_id, 20) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $log->reason ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ $log->created_at ? $log->created_at->format('d.m.Y H:i:s') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="bg-gray-50 dark:bg-gray-800 px-6 py-3 border-t border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                Showing <span class="font-medium">{{ $authlogs->count() }}</span> authentication log(s)
            </p>
        </div>
    @else
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No logs</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No authentication logs found for this customer.</p>
        </div>
    @endif
</div>