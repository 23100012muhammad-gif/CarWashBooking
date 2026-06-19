<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin CarWash',
            'email' => 'admin@carwash.com',
            'password' => Hash::make('admin123'),
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        // Create regular user for testing
        User::create([
            'name' => 'User Test',
            'email' => 'user@test.com',
            'password' => Hash::make('user123'),
            'is_admin' => false,
            'email_verified_at' => now(),
        ]);
    }
}
