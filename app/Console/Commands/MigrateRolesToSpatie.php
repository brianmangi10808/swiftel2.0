<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class MigrateRolesToSpatie extends Command
{
    protected $signature = 'roles:migrate-to-spatie';
    protected $description = 'Migrate string-based roles to Spatie permission system';

    public function handle()
    {
        $this->info('Starting role migration...');

        // Get all users with string roles
        $users = User::whereNotNull('role')->get();

        foreach ($users as $user) {
            if ($user->role) {
                // Find or create the role
                $role = Role::firstOrCreate([
                    'name' => $user->role,
                    'guard_name' => 'web'
                ]);

                // Assign role to user using Spatie
                if (!$user->hasRole($role->name)) {
                    $user->assignRole($role->name);
                    $this->info("Assigned role '{$role->name}' to user: {$user->email}");
                }
            }
        }

        $this->info('Role migration completed!');
        $this->newLine();
        $this->warn('You can now remove the "role" column from users table if no longer needed.');
    }
}