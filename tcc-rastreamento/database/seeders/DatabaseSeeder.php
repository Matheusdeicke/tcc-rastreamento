<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,       // 1) cria os papéis
            AdminUserSeeder::class,  // 2) cria o super admin
            DemoUsersSeeder::class,  // 3) cria usuários de demo (enfermagem e cme)
        ]);
    }
}
