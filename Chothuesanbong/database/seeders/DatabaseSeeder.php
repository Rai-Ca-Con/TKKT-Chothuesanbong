<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::insert([
            'id' => "admin_000",
            'name' => 'Admin',
            'email' => 'admin@ex.com',
            'password' => Hash::make(123),
            'is_admin' => true
        ]);

        User::insert([
            'id' => "user_001",
            'name' => 'Test user',
            'email' => 'test1@ex.com',
            'password' => Hash::make(123),
        ]);

        $this->call([
            CategorySeeder::class,
            StateSeeder::class,
            FieldSeeder::class,
        ]);
    }
}
