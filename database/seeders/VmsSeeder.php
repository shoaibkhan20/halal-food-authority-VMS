<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use App\Models\VehicleType; // Ensure this is correctly imported
use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Models\Vehicle;
class VmsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 1. Branches
        $branches = [];
        for ($i = 1; $i <= 5; $i++) {
            $branches[] = DB::table('branches')->insertGetId([
                'name' => 'Branch ' . $i,
                'location' => $faker->city,
                'address' => $faker->address,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Users (using existing role_ids)
        $roleIds = DB::table('user_roles')->pluck('id')->toArray();
        $users = [];
        for ($i = 1; $i <= 10; $i++) {
            $users[] = DB::table('users')->insertGetId([
                'name' => $faker->name,
                'contact' => $faker->phoneNumber,
                'username' => 'user' . $i . '_' . Str::random(3),
                'password' => 'password123', // Replace this with a hashed password if needed
                'branch_id' => $faker->randomElement($branches),
                'role_id' => $faker->randomElement($roleIds),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Vehicles
        // Fetch all vehicle type names from the vehicle_types table
        $vehicleTypeNames = VehicleType::pluck('name')->toArray(); // Array of vehicle type names

        // Fetch all branch IDs directly from the previously created branch data
        $branchIds = $branches;

        $vehicleIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $regId = 'REG' . strtoupper(Str::random(5));
            $vehicleIds[] = $regId;
            DB::table('vehicles')->insert([
                'RegID' => $regId,
                'Model' => $faker->randomElement(['Toyota Corolla', 'Suzuki Mehran', 'Honda Civic', 'Kia Sportage']),
                'Fuel_type' => $faker->randomElement(['Petrol', 'Diesel']),
                'Vehicle_Type' => $faker->randomElement($vehicleTypeNames), // Random vehicle type from the database
                'branch_id' => $faker->randomElement($branchIds),
                'Average_mileage' => $faker->randomFloat(2, 8, 20),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Vehicle Assignments
        for ($i = 1; $i <= 8; $i++) {
            DB::table('vehicle_assignments')->insert([
                'vehicle_id' => $faker->randomElement($vehicleIds),
                'user_id' => $faker->randomElement($users),
                'assigned_date' => $faker->date,
                'returned_date' => $faker->optional()->date,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 5. Locations
        for ($i = 1; $i <= 20; $i++) {
            DB::table('locations')->insert([
                'vehicle_id' => $faker->randomElement($vehicleIds),
                'latitude' => $faker->latitude,
                'longitude' => $faker->longitude,
                'speed' => $faker->randomFloat(2, 20, 120),
                'timestamp' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 6. Logbooks
        for ($i = 1; $i <= 10; $i++) {
            DB::table('logbooks')->insert([
                'vehicle_id' => $faker->randomElement($vehicleIds),
                'user_id' => $faker->randomElement($users),
                'trip_from' => $faker->city,
                'trip_to' => $faker->city,
                'description' => $faker->sentence,
                'distance_covered' => $faker->numberBetween(10, 300),
                'fuel_used' => $faker->randomFloat(2, 2, 15),
                'trip_date' => $faker->date,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 7. Maintenance Requests


        // Use the Eloquent model for insert
        $maintenanceIds = [];

        for ($i = 1; $i <= 5; $i++) {
            $appliedBy = $faker->randomElement($users);
            $vehicleId = $faker->randomElement($vehicleIds);

            // Director decision
            $directorStatus = $faker->randomElement(['approved', 'rejected', 'pending']);
            $directorReviewedBy = $directorStatus !== 'pending' ? $faker->randomElement($users) : null;
            $directorRejectionMessage = $directorStatus === 'rejected' ? $faker->sentence : null;

            // Committee status only matters if director rejected
            $committeeStatus = 'pending';
            $committeeReviewedBy = null;
            $committeeRejectionMessage = null;

            if ($directorStatus === 'rejected') {
                $committeeStatus = $faker->randomElement(['approved', 'rejected', 'pending']);
                $committeeReviewedBy = $committeeStatus !== 'pending' ? $faker->randomElement($users) : null;
                $committeeRejectionMessage = $committeeStatus === 'rejected' ? $faker->sentence : null;
            }

            // Final approval only if committee approved
            $directorFinalStatus = 'pending';
            $directorFinalApprovedBy = null;
            $directorFinalRejectionMessage = null;

            if ($committeeStatus === 'approved') {
                $directorFinalStatus = $faker->randomElement(['approved', 'rejected', 'pending']);
                $directorFinalApprovedBy = $directorFinalStatus !== 'pending' ? $faker->randomElement($users) : null;
                $directorFinalRejectionMessage = $directorFinalStatus === 'rejected' ? $faker->sentence : null;
            }

            // Create record
            $maintenanceRequest = MaintenanceRequest::create([
                'vehicle_id' => $vehicleId,
                'applied_by' => $appliedBy,
                'issue' => $faker->words(3, true),
                'estimated_cost' => $faker->randomFloat(2, 5000, 30000),

                'director_status' => $directorStatus,
                'director_reviewed_by' => $directorReviewedBy,
                'director_rejection_message' => $directorRejectionMessage,

                'committee_status' => $committeeStatus,
                'committee_reviewed_by' => $committeeReviewedBy,
                'committee_rejection_message' => $committeeRejectionMessage,

                'director_final_status' => $directorFinalStatus,
                'director_final_approved_by' => $directorFinalApprovedBy,
                'director_final_rejection_message' => $directorFinalRejectionMessage,
            ]);

            $maintenanceIds[] = $maintenanceRequest->id;
        }



        // 8. Supervisor Reports
        foreach ($maintenanceIds as $mid) {
            DB::table('vehicle_supervisor_reports')->insert([
                'maintenance_request_id' => $mid,
                'user_id' => $faker->randomElement($users),
                'report' => $faker->paragraph,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 9. Fuel Requests
        for ($i = 1; $i <= 10; $i++) {
            DB::table('fuel_requests')->insert([
                'vehicle_id' => $faker->randomElement($vehicleIds),
                'user_id' => $faker->randomElement($users),
                'fuel_amount' => $faker->randomFloat(2, 10, 60),
                'status' => $faker->randomElement(['pending', 'approved', 'rejected']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 10. Vehicle Maintenance (NEW BLOCK)
        foreach ($maintenanceIds as $mid) {
            DB::table('vehicle_maintenance')->insert([
                'maintenance_request_id' => $mid,
                'vehicle_id' => DB::table('maintenance_requests')->where('id', $mid)->value('vehicle_id'),
                'status' => $faker->randomElement(['completed', 'in_progress', 'cancelled']),
                'started_at' => now()->subDays(rand(2, 10)),
                'completed_at' => now(),
                'actual_cost' => $faker->randomFloat(2, 3000, 15000),
                'maintenance_notes' => $faker->sentence,
                'performed_by' => $faker->randomElement($users),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
