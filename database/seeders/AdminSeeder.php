<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'potayrexd@gmail.com'],
            [
                'name' => 'Queen\'s Cup Admin',
                'password' => Hash::make('Admin123@'),
                'role' => 'admin',
            ]
        );
    }
}
