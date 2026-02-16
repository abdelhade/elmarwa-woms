<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Permission;

class AssignAllPermissionsToUser1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::find(1);
        
        if (!$user) {
            $this->command->error('User with ID 1 not found!');
            return;
        }

        // Get all permissions
        $allPermissions = Permission::all()->pluck('id');

        // Assign all permissions to user 1
        $user->permissions()->sync($allPermissions);

        $this->command->info("All {$allPermissions->count()} permissions assigned to user: {$user->name}");
    }
}
