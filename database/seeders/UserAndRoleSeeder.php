<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserRole;

class UserAndRoleSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $roles = [
            'super-admin',
            'director-admin',
            'divisional-user',
            'district-user',
            'committe-user',
            'individual-driver',
            'vehicle-supervisor'
        ];
        
        foreach ($roles as $roleName) {
            UserRole::firstOrCreate(['role_name' => $roleName]);
        }
        // Create super-admin user
        User::firstOrCreate(
            ['contact' => 'admin@example.com'],
            [
                'name' => 'SuperAdmin',
                'username' => 'superadmin',
                'password' => 'password123', // 👈 Change in production
                'role_id' => UserRole::where('role_name', 'super-admin')->first()->id,
                'branch_id' => null // Or set a valid branch ID
            ]
        );
    }
}

