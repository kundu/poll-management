<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'user_type' => User::USER_TYPE_ADMIN,
            ]
        );

        // Create a regular user for testing
        User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password123'),
                'user_type' => User::USER_TYPE_USER,
            ]
        );

        $this->command->info('Admin and test users created successfully!');
        $this->command->info('Admin Email: admin@example.com');
        $this->command->info('Admin Password: admin123');
        $this->command->info('User Email: user@example.com');
        $this->command->info('User Password: password123');
    }
}
