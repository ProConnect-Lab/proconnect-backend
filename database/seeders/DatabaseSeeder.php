<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password');

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => $defaultPassword,
        ]);

        User::factory()
            ->admin()
            ->create([
                'name' => 'Admin Général',
                'email' => 'admin@proconnect.test',
                'password' => $defaultPassword,
            ]);
    }
}
