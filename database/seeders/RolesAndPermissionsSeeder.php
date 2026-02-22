<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = ['view', 'create', 'edit', 'delete'];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                ['guard_name' => 'web']
            );
        }

        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'web']
        );
        $adminRole->syncPermissions(Permission::all());

        Role::firstOrCreate(
            ['name' => 'user'],
            ['guard_name' => 'web']
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );

        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
    }
}
