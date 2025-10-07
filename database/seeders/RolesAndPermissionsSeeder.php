<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cached permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // User permissions
            'user.browse',
            'user.show',
            'user.edit',
            'user.add',
            'user.delete',
            'user.trash.recover.one',
            'user.trash.remove.one',
            'user.trash.empty.all',
            'user.trash.restore.all',

            // Joke permissions
            'joke.browse',
            'joke.show',
            'joke.edit',
            'joke.add',
            'joke.delete',
            'joke.trash.recover.one',
            'joke.trash.remove.one',
            'joke.trash.empty.all',
            'joke.trash.restore.all',

            // Admin-only
            'roles.manage',
        ];

        // Create permissions
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Create Roles
        $admin = Role::firstOrCreate(['name' => 'Administrator']);
        $staff = Role::firstOrCreate(['name' => 'Staff']);
        $client = Role::firstOrCreate(['name' => 'Client']);

        // Assign permissions to roles
        $admin->syncPermissions(Permission::all());

        $staff->syncPermissions([
            'user.browse',
            'user.show',
            'user.edit',
            'user.add',
            'user.delete',
            'joke.browse',
            'joke.show',
            'joke.edit',
            'joke.delete',
            'joke.trash.recover.one',
            'joke.trash.remove.one',
        ]);

        $client->syncPermissions([
            'joke.browse',
            'joke.show',
            'joke.edit',
            'joke.add',
            'joke.delete',
            'joke.trash.recover.one',
            'joke.trash.remove.one',
            'joke.trash.empty.all',
            'joke.trash.restore.all',
        ]);
    }
}
