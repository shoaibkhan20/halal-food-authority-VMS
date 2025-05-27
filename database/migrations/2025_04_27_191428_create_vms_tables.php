<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVmsTables extends Migration
{
    public function up()
    {
        // 1. Branches Table
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });

        // 2. User Roles Table
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name')->unique();
            $table->timestamps();
        });

        // 3. Users Table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact');
            $table->string('username')->nullable()->unique();
            $table->string('password');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->foreignId('role_id')->nullable()->constrained('user_roles')->onDelete('set null');
            $table->rememberToken();
            $table->timestamps();
        });

        // 4. Vehicle Types Table (New table for predefined vehicle types)
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Name of the vehicle type (e.g., Sedan, SUV)
            $table->timestamps();
        });

        // Insert some default vehicle types
        DB::table('vehicle_types')->insert([
            ['name' => 'Personally_alot'],
            ['name' => 'Mobile_lab'],
            ['name' => 'Office_cars'],
        ]);

        // 5. Vehicles Table (Modify the existing table to reference the new vehicle_types table)
        Schema::create('vehicles', function (Blueprint $table) {
            $table->string('RegID')->primary();
            $table->string('Model');
            $table->string('Fuel_type');
            $table->string('Vehicle_Type');  // This field will store the name of the vehicle type
            // $table->string('Region');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->float('Average_mileage')->nullable();
            $table->string('status')->default('Available');
            $table->timestamps();
        });

        // 6. Vehicle Assignments Table
        Schema::create('vehicle_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_id');
            $table->foreign('vehicle_id')->references('RegID')->on('vehicles')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('assigned_date');
            $table->date('returned_date')->nullable();
            $table->timestamps();
        });

        // 7. Locations Table
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_id');
            $table->foreign('vehicle_id')->references('RegID')->on('vehicles')->onDelete('cascade');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->float('speed')->nullable();
            $table->timestamp('timestamp');
            $table->timestamps();
        });

        // 8. Logbooks Table
        Schema::create('logbooks', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_id');
            $table->foreign('vehicle_id')->references('RegID')->on('vehicles')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('trip_from');
            $table->string('trip_to');
            $table->text('description')->nullable();
            $table->integer('distance_covered')->nullable();
            $table->float('fuel_used')->nullable();
            $table->date('trip_date');
            $table->timestamps();
        });

        // 9. Maintenance Requests Table
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();

            $table->string('vehicle_id');
            $table->foreign('vehicle_id')->references('RegID')->on('vehicles')->onDelete('cascade');

            $table->foreignId('applied_by')->nullable()->constrained('users')->onDelete('set null');

            // Status Tracking
            $table->enum('status', ['pending', 'under_committee_review', 'committee_approved', 'committee_rejected', 'final_approved', 'final_rejected'])->default('pending');

            // Director's Initial Review
            $table->enum('director_status', ['pending', 'approved', 'rejected','waiting_for_committee'])->default('pending');
            $table->foreignId('director_reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('director_rejection_message')->nullable();

            // Committee Review
            $table->enum('committee_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('committee_reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('committee_rejection_message')->nullable();

            // Director Final Approval (after committee approval)
            $table->enum('director_final_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('director_final_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('director_final_rejection_message')->nullable();

            $table->string('issue');
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->timestamps();
        });


        // 10. Vehicle Maintenance Table
        Schema::create('vehicle_maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_request_id')->constrained('maintenance_requests')->onDelete('cascade');
            $table->string('vehicle_id');
            $table->foreign('vehicle_id')->references('RegID')->on('vehicles')->onDelete('cascade');
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->text('maintenance_notes')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 11. Vehicle Supervisor Reports Table
        Schema::create('vehicle_supervisor_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_request_id')->constrained('maintenance_requests')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('report');
            $table->timestamps();
        });

        // 12. Fuel Requests Table
        Schema::create('fuel_requests', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_id');
            $table->foreign('vehicle_id')->references('RegID')->on('vehicles')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('fuel_amount', 8, 2);
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fuel_requests');
        Schema::dropIfExists('vehicle_supervisor_reports');
        Schema::dropIfExists('vehicle_maintenance');
        Schema::dropIfExists('maintenance_requests');
        Schema::dropIfExists('logbooks');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('vehicle_assignments');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('vehicle_types');
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('branches');
    }
}
