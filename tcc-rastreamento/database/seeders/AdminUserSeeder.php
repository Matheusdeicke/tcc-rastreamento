<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'admin@cme.com';

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'     => 'Super Admin',
                'password' => bcrypt('senha123'), // troca depois ;)
            ]
        );

        if (! $user->hasRole('admin')) {
            $user->assignRole('admin');
        }
    }
}
