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
    

        User::create([
        'name' => 'Super Admin',
        'email' => 'superadmin@test.com',
        'password' => Hash::make('password'),
        'role' => 'super_admin',
    ]);

    User::create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);

    User::create([
        'name' => 'Team Lead',
        'email' => 'teamlead@test.com',
        'password' => Hash::make('password'),
        'role' => 'team_lead',
    ]);
    }
}
