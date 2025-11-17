<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use RouterOS\Client;
use RouterOS\Query;

class FastCustomerStatusService
{
    /**
     * Check all customers' status across all routers (bulk operation)
     */
    public function checkAllStatuses(): array
    {
        $routers = Device::all();
        $allActiveSessions = [];

        // Use concurrent requests if possible
        foreach ($routers as $router) {
            $sessions = $this->getActiveSessionsFromRouter($router);
            $allActiveSessions = array_merge($allActiveSessions, $sessions);
        }

        // Remove duplicates
        $allActiveSessions = array_unique($allActiveSessions);

        // Bulk update database
        DB::table('customers')->update(['status' => 'offline']);
        
        if (!empty($allActiveSessions)) {
            // Update in chunks to avoid query size limits
            $chunks = array_chunk($allActiveSessions, 500);
            foreach ($chunks as $chunk) {
                DB::table('customers')
                    ->whereIn('username', $chunk)
                    ->update(['status' => 'online']);
            }
        }

        return [
            'online' => count($allActiveSessions),
            'total' => DB::table('customers')->count(),
        ];
    }

    /**
     * Get all active sessions from a single router
     */
    private function getActiveSessionsFromRouter(Device $router): array
    {
        try {
            $client = new Client([
                'host' => $router->ip_address,
                'user' => $router->username,
                'pass' => $router->password,
                'port' => $router->api_port ?? 8728,
                'timeout' => 5,
            ]);

            $query = new Query('/ppp/active/print');
            $response = $client->query($query)->read();

            $sessions = [];
            foreach ($response as $session) {
                if (isset($session['name'])) {
                    $sessions[] = $session['name'];
                }
            }

            return $sessions;

        } catch (\Throwable $e) {
           
            return [];
        }
    }

    /**
     * Check single customer status (for individual checks)
     */
    public function checkStatus($customer): string
    {
        $routers = Device::all();

        foreach ($routers as $router) {
            try {
                $client = new Client([
                    'host' => $router->ip_address,
                    'user' => $router->username,
                    'pass' => $router->password,
                    'port' => $router->api_port ?? 8728,
                    'timeout' => 3,
                ]);

                $query = (new Query('/ppp/active/print'))->where('name', $customer->username);
                $response = $client->query($query)->read();

                if (!empty($response)) {
                    $customer->update(['status' => 'online']);
                    return 'online';
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        $customer->update(['status' => 'offline']);
        return 'offline';
    }
}