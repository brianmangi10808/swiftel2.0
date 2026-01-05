<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Permission;

class UserPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::where('is_super_admin', 1)->first();
        if ($superAdmin) {
            $superAdmin->permissions()->sync(Permission::pluck('id')->toArray());
        }

        $admins = User::where('role', 'administrator')->get();
        foreach ($admins as $admin) {
            $admin->permissions()->sync(
                Permission::whereIn('action', ['create', 'read', 'update', 'delete'])
                    ->pluck('id')
                    ->toArray()
            );
        }

        $technicals = User::where('role', 'technical')->get();
        foreach ($technicals as $tech) {
            $tech->permissions()->sync(
                Permission::where('action', 'read')->pluck('id')->toArray()
            );
        }
    }
}
