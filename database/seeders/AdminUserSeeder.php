<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'Admin']);

        // Membuat user admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@jussy.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('jussy'), // ganti password sesuai kebutuhan
            ]
        );

        // Assign role
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
    }
}
