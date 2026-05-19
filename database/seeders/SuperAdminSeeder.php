<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's default SuperAdmin.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'superadmin@novuflow.com',
        ], [
            'name' => 'SuperAdmin',
            'password' => Hash::make('Password123!'),
            'role' => 'SuperAdmin',
        ]);
    }
}
