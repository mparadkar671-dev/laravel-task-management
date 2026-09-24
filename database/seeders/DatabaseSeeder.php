<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Initializes core Spatie roles for deployment.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
    }
}
