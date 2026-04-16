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
        'team_id' => null,
    ]);

    User::create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => Hash::make('password'),
        'role' => 'admin',
        'team_id' => null,
    ]);

    User::create([
        'name' => 'Team Lead',
        'email' => 'teamlead@test.com',
        'password' => Hash::make('password'),
        'role' => 'team_lead',
        'team_id' => 1,
    ]);

    // Example team member for team 1
    User::create([
        'name' => 'Member One',
        'email' => 'member1@test.com',
        'password' => Hash::make('password'),
        'role' => 'user',
        'team_id' => 1,
    ]);

    // Example team 2
    User::create([
        'name' => 'Team Lead 2',
        'email' => 'teamlead2@test.com',
        'password' => Hash::make('password'),
        'role' => 'team_lead',
        'team_id' => 2,
    ]);

    User::create([
        'name' => 'Member Two',
        'email' => 'member2@test.com',
        'password' => Hash::make('password'),
        'role' => 'user',
        'team_id' => 2,
    ]);
    }
}
