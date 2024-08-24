<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane.smith@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
