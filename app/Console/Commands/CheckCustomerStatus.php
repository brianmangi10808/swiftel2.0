<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Device;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RouterOS\Client;
use RouterOS\Query;

class CheckCustomerStatus extends Command
{
    protected $signature = 'customers:check-status {--chunk=100}';
    protected $description = 'Check PPPoE status for all customers (optimized)';

    public function handle()
    {
        $this->info('Fetching all active PPPoE sessions from routers...');
        
        // Step 1: Get all active sessions from all routers at once
        $activeSessions = $this->getAllActiveSessions();
        
        if (empty($activeSessions)) {
            $this->warn('No active sessions found on any router!');
        } else {
            $this->info('Found ' . count($activeSessions) . ' active sessions');
        }
        
        // Step 2: Bulk update - mark all as offline first
        $this->info('Updating customer statuses...');
        DB::table('customers')->update(['status' => 'offline']);
        
        // Step 3: Mark only active ones as online
        if (!empty($activeSessions)) {
            DB::table('customers')
                ->whereIn('username', $activeSessions)
                ->update(['status' => 'online']);
        }
        
        $onlineCount = DB::table('customers')->where('status', 'online')->count();
        $offlineCount = DB::table('customers')->where('status', 'offline')->count();
        
        $this->info("✓ Status update completed!");
        $this->table(
            ['Status', 'Count'],
            [
                ['Online', $onlineCount],
                ['Offline', $offlineCount],
            ]
        );
        
        return Command::SUCCESS;
    }

    /**
     * Fetch all active PPPoE sessions from all routers
     */
    private function getAllActiveSessions(): array
    {
        $routers = Device::all();
        $activeSessions = [];
        
        $bar = $this->output->createProgressBar($routers->count());
        $bar->setFormat('Checking routers: %current%/%max% [%bar%] %percent:3s%%');
        
        foreach ($routers as $router) {
            try {
                // Use same config as CustomerStatusService
                $config = [
                    'host' => $router->ip_address,
                    'user' => $router->username,
                    'pass' => $router->password,
                ];
                
                // Only add port if it's explicitly set
                if (!empty($router->api_port)) {
                    $config['port'] = (int)$router->api_port;
                }
                
                // Only add timeout if needed
                if (!isset($config['timeout'])) {
                    $config['timeout'] = 3;
                }
                
                $client = new Client($config);

                $query = new Query('/ppp/active/print');
                $response = $client->query($query)->read();

                $count = 0;
                foreach ($response as $session) {
                    if (isset($session['name'])) {
                        $activeSessions[] = $session['name'];
                        $count++;
                    }
                }
                
                $this->info("\n✓ Router {$router->ip_address}: Found {$count} active sessions");
                
            } catch (\Throwable $e) {
                $this->error("\n✗ Router {$router->ip_address} failed: " . $e->getMessage());
                $this->line("   Config used: " . json_encode([
                    'host' => $router->ip_address,
                    'port' => $router->api_port ?? 'default',
                    'user' => $router->username,
                ]));
                continue;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        // Remove duplicates (in case same user is on multiple routers)
        return array_unique($activeSessions);
    }
}