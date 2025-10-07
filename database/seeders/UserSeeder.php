<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seedUsers = [
            [
                'id' => 100,
                'name' => 'Admin Istrator',
                'email' => 'admin@example.com',
                'password' => 'Password1',
                'email_verified_at' => now(),
                'role' => 'Administrator',
            ],
            [
                'id' => 200,
                'name' => 'Staff User',
                'email' => 'staff@example.com',
                'password' => 'Password1',
                'email_verified_at' => now(),
                'role' => 'Staff',
            ],
            [
                'id' => 201,
                'name' => 'Client User',
                'email' => 'client@example.com',
                'password' => 'Password1',
                'email_verified_at' => now(),
                'role' => 'Client',
                'assigned_staff_id' => 200,
            ],
            [
                'id' => 202,
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'Password1',
                'email_verified_at' => now(),
                'role' => 'Client',
            ],
            [
                'id' => 203,
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'password' => 'Password1',
                'email_verified_at' => now(),
                'role' => 'Client',
            ],
            [
                'id' => 204,
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'Password1',
                'email_verified_at' => now(),
                'role' => 'Client',
                'assigned_staff_id' => 200,
            ],
        ];

        foreach ($seedUsers as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(
                ['id' => $userData['id']],
                array_merge($userData, [
                    'password' => bcrypt($userData['password']),
                ])
            );

            $user->assignRole($role);
        }
    }
}
