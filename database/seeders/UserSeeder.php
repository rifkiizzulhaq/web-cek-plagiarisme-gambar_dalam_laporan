<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Gunakan firstOrCreate untuk mencegah duplicate entry
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('12345678'),
                'role_id' => 1,
            ]
        );
        
        // User::firstOrCreate(
        //     ['email' => 'rifki@gmail.com'],
        //     [
        //         'name' => 'rifki',
        //         'password' => Hash::make('12345678'),
        //         'role_id' => 2,
        //     ]
        // );
    }
}
