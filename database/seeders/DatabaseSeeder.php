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
        $admin = User::firstOrNew(['email' => 'admin@lab.com']);
        $admin->name = 'Admin Lab';
        $admin->password = bcrypt('password');
        $admin->role = 'admin';
        $admin->email_verified_at = now();
        $admin->save();

        // Regular User
        $user = User::firstOrNew(['email' => 'user@lab.com']);
        $user->name = 'User Lab';
        $user->password = bcrypt('password');
        $user->role = 'user';
        $user->save();

        $this->call(InventoryItemSeeder::class);
        $this->call(HistoricalActivitySeeder::class);
    }
}
