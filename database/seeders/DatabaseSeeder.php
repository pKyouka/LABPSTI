<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Categories
        \App\Models\Category::firstOrCreate(['name' => 'Data Engineering']);
        \App\Models\Category::firstOrCreate(['name' => 'Digital Forensik']);

        // Admin User
        User::updateOrCreate(
            ['email' => 'admin@lab.com'],
            [
                'name' => 'Admin Lab',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Regular User
        User::updateOrCreate(
            ['email' => 'user@lab.com'],
            [
                'name' => 'User Lab',
                'password' => bcrypt('password'),
                'role' => 'user',
            ]
        );

        $this->call(HistoricalActivitySeeder::class);
    }
}
