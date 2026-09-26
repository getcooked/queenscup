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
        // The repository is public, so the password comes from the
        // environment and is only used to create the first admin. An existing
        // admin keeps their password; re-seeding must never reset it.
        $password = env('ADMIN_PASSWORD');

        if (! $password) {
            $this->command?->warn('ADMIN_PASSWORD is not set, so no admin account was created.');

            return;
        }

        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL') ?: 'potayrexd@gmail.com'],
            [
                'name' => 'Queen\'s Cup Admin',
                'password' => Hash::make($password),
                'role' => 'admin',
            ]
        );
    }
}
