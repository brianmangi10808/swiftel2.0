<?php

namespace Database\Seeders;
use Spatie\Permission\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'administrator']);
    Role::firstOrCreate(['name' => 'technical']);

    }
   

}
