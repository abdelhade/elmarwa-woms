<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create permissions first
        $this->call(PermissionsSeeder::class);
        
        // 2. Create admin user
        $this->call(AdminUserSeeder::class);
        
        // 3. Assign all permissions to user 1
        $this->call(AssignAllPermissionsToUser1Seeder::class);
    }
}
