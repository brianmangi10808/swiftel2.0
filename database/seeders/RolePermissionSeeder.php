<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('🚀 Creating permissions...');

        // Define all models
        $models = [
            'companies', 'customers', 'devices', 'services', 'users',
            'groups', 'premises', 'sectors', 'payments', 'tickets',
            'messages', 'leads', 'settings', 'system_settings',
            'sms_gateways', 'sms_providers', 'sms_templates',
            'activity_logs', 'audits', 'pppoe_traffic','roles',
        ];

        $actions = ['create', 'read', 'update', 'delete'];

        // Create permissions (use firstOrCreate to avoid duplicates)
        foreach ($models as $model) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(
                    ['name' => "{$action} {$model}", 'guard_name' => 'web']
                );
            }
        }

        $this->command->info('✅ ' . (count($models) * count($actions)) . ' permissions created/verified!');
        $this->command->newLine();
        $this->command->info('🚀 Creating roles...');

        // Create roles (use firstOrCreate to avoid duplicates)
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $administrator = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);
        $technical = Role::firstOrCreate(['name' => 'technical', 'guard_name' => 'web']);
        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $companyAdmin = Role::firstOrCreate(['name' => 'company_admin', 'guard_name' => 'web']);

        $this->command->info('✅ 5 roles created/verified!');
        $this->command->newLine();
        $this->command->info('🚀 Assigning permissions to roles...');

        // Super Admin - ALL permissions (use syncPermissions to avoid duplicates)
        $superAdmin->syncPermissions(Permission::all());
        $this->command->info('✅ Super Admin: ALL permissions');

        // Administrator - Most permissions
        $adminPermissions = [
            'create users', 'read users', 'update users', 'delete users',
            'create companies', 'read companies', 'update companies', 'delete companies',
            'create customers', 'read customers', 'update customers', 'delete customers',
            'create services', 'read services', 'update services', 'delete services',
            'create groups', 'read groups', 'update groups', 'delete groups',
            'create premises', 'read premises', 'update premises', 'delete premises',
            'create sectors', 'read sectors', 'update sectors', 'delete sectors',
            'create payments', 'read payments', 'update payments', 'delete payments',
            'create tickets', 'read tickets', 'update tickets', 'delete tickets',
            'create messages', 'read messages', 'update messages', 'delete messages',
            'create leads', 'read leads', 'update leads', 'delete leads',
            'read activity_logs', 'read audits',
            'read settings', 'update settings','read roles','update roles',
        ];
        $administrator->syncPermissions($adminPermissions);
        $this->command->info('✅ Administrator: ' . count($adminPermissions) . ' permissions');

        // Technical - Technical operations
        $technicalPermissions = [
            'read users',
            'read companies', 'read customers',
            'create devices', 'read devices', 'update devices', 'delete devices',
            'read services', 'update services',
            'create tickets', 'read tickets', 'update tickets',
            'create messages', 'read messages',
            'read pppoe_traffic', 'update pppoe_traffic',
            'read activity_logs','read roles','update roles',
        ];
        $technical->syncPermissions($technicalPermissions);
        $this->command->info('✅ Technical: ' . count($technicalPermissions) . ' permissions');

        // Staff - Basic operations
        $staffPermissions = [
            'read customers',
            'create tickets', 'read tickets', 'update tickets',
            'create messages', 'read messages',
            'read payments',
            'create leads', 'read leads', 'update leads','read roles','update roles',
        ];
        $staff->syncPermissions($staffPermissions);
        $this->command->info('✅ Staff: ' . count($staffPermissions) . ' permissions');

        // Company Admin - Company-specific management
        $companyAdminPermissions = [
            'read users', 'create users', 'update users',
            'read customers', 'create customers', 'update customers', 'delete customers',
            'read devices', 'create devices', 'update devices', 'delete devices',
            'read services', 'create services', 'update services', 'delete services',
            'read payments', 'create payments', 'update payments',
            'read tickets', 'create tickets', 'update tickets',
            'read messages', 'create messages',
            'read leads', 'create leads', 'update leads',
            'read settings', 'update settings','read roles','update roles',
        ];
        $companyAdmin->syncPermissions($companyAdminPermissions);
        $this->command->info('✅ Company Admin: ' . count($companyAdminPermissions) . ' permissions');

        $this->command->newLine();
        $this->command->info('🚀 Creating/Updating super admin user...');

        // Create or update super admin user
        $superUser = User::updateOrCreate(
            ['email' => 'super@swiftel.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'is_super_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Assign super_admin role (syncRoles removes old roles and adds new ones)
        $superUser->syncRoles(['super_admin']);
        $this->command->info('✅ Super admin user created/updated');

        $this->command->newLine();
        $this->command->info('🎉 Seeding completed successfully!');
        $this->command->info('');
        $this->command->warn('Login Credentials:');
        $this->command->info('Email: super@swiftel.com');
        $this->command->info('Password: password');
    }
}