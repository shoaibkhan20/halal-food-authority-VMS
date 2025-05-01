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
        // Seed user roles and initial super admin
        // $this->call(UserAndRoleSeeder::class);

        // Seed all other tables (excluding user_roles)
        $this->call(VmsSeeder::class);
    }
}
