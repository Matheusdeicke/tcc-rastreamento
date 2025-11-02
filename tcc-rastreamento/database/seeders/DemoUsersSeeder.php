<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Enfermagem
        $nurse = User::firstOrCreate(
            ['email' => 'enfermagem@demo.com'],
            ['name' => 'Enfermagem Demo', 'password' => Hash::make('senha123')]
        );
        $nurse->syncRoles(['enfermagem']); // só enfermeiro

        // CME
        $cme = User::firstOrCreate(
            ['email' => 'cme@demo.com'],
            ['name' => 'CME Demo', 'password' => Hash::make('senha123')]
        );
        $cme->syncRoles(['cme']); // pode trocar para ['admin'] se preferir
    }
}
