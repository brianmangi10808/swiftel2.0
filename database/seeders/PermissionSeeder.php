<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $tables = [
            'companies',
            'customers',
            'devices',
            'services',
            'users',
            'groups',
            'premises',
            'sectors',
            'payments',
            'tickets',
            'messages',
            'leads',
            'settings',
            'system_settings',
            'sms_gateways',
            'sms_providers',
            'sms_templates',
            'activity_logs',
            'audits',
            'pppoe_traffic',
        ];

        $actions = ['create', 'read', 'update', 'delete'];

        foreach ($tables as $table) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'model' => $table,
                    'action' => $action,
                ]);
            }
        }
    }
}
