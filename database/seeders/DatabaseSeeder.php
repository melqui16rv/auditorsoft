<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesOciSeeder::class,
            ParametrizacionBasicaSeeder::class,
            MunicipiosColombiaSeeder::class,
            UsersSeeder::class,
            PAASeeder::class,
            PAASeguimientoSeeder::class,
            EvidenciaSeeder::class,
        ]);
    }
}
