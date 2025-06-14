<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'contact',
        'username',
        'password',
        'role_id',
        'branch_id',
    ];
    public function role()
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }
    public function hasRole($role)
    {
        // Adjust this based on your actual role implementation
        return $this->role === $role;
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function vehicleAssignments()
    {
        return $this->hasMany(VehicleAssignment::class);
    }
    public function currentVehicleAssignments()
    {
        return $this->hasOne(VehicleAssignment::class)->whereNull('returned_date');
    }

    public function logbooks()
    {
        return $this->hasMany(Logbook::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'applied_by');
    }

    public function performedMaintenance()
    {
        return $this->hasMany(VehicleMaintenance::class, 'performed_by');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }
}
