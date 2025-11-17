<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Device;
use RouterOS\Client;
use RouterOS\Query;

class CustomerStatusService
{
    /**
     * Checks if the given customer is online across all MikroTik routers
     */
    public function checkStatus(Customer $customer): string
    {
        $routers = Device::all();

        foreach ($routers as $router) {
            try {
                $client = new Client([
                    'host' => $router->ip_address,
                    'user' => $router->username,
                    'pass' => $router->password,
                    'port' => $router->api_port,
                    'timeout' => 3,
                ]);

                // Look in PPPoE active list
                $query = (new Query('/ppp/active/print'))->where('name', $customer->username);
                $response = $client->query($query)->read();

                if (!empty($response)) {
                    $customer->update(['status' => 'online']);
                    return 'online';
                }
            } catch (\Throwable $e) {
                // If router unreachable, just continue to next one
                continue;
            }
        }

        // Not found on any router → offline
        $customer->update(['status' => 'offline']);
        return 'offline';
    }
}
