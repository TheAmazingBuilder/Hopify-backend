<?php

declare(strict_types=1);

namespace App\Modules\Hr\Models;

use App\Modules\Foundation\Models\Department;
use App\Modules\Foundation\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Shared\Enums\EmployeeRoleType;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'employees';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'department_uuid',
        'employee_number',
        'fname',
        'lname',
        'role_type',
        'specialization',
        'license_number',
        'phone',
        'email',
        'dob',
        'hire_date',
        'termination_date',
        'photo_path',
        'signature_path',
        'is_active',
    ];

   
    protected $casts = [
        'role_type' => EmployeeRoleType::class,
        'dob' => 'date',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(
            Department::class,
            'department_uuid',
            'uuid'
        );
    }

    public function user(): HasOne
    {
        return $this->hasOne(
            User::class,
            'employee_uuid',
            'uuid'
        );
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeDoctors(Builder $query): Builder
    {
        return $query->where('role_type',EmployeeRoleType::Doctor->value);
    }

    public function scopeNurses(Builder $query): Builder
    {
        return $query->where('role_type',EmployeeRoleType::Nurse->value);
    }

    public function scopePharmacists(Builder $query): Builder
    {
        return $query->where('role_type',EmployeeRoleType::Pharmacist->value);
    }


    public function scopeTechnicians(Builder $query): Builder
    {
        return $query->where('role_type',EmployeeRoleType::Technician->value);
    }

    public function scopeReceptionists(Builder $query): Builder
    {
        return $query->where('role_type',EmployeeRoleType::Receptionist->value);
    }

    public function scopeAdministrators(Builder $query): Builder
    {
        return $query->where('role_type',EmployeeRoleType::Admin->value);
    }

    public function scopeDirectors(Builder $query): Builder
    {
        return $query->where('role_type',EmployeeRoleType::Director->value);
    }

    public function scopeForDepartment(Builder $query,string $departmentUuid): Builder 
    {
        return $query->where('department_uuid',$departmentUuid);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

        public function isDoctor(): bool
    {
        return $this->role_type === EmployeeRoleType::Doctor;
    }

    public function isNurse(): bool
    {
        return $this->role_type === EmployeeRoleType::Nurse;
    }

    public function isPharmacist(): bool
    {
        return $this->role_type === EmployeeRoleType::Pharmacist;
    }

    public function hasUserAccount(): bool
    {
        return $this->user()->exists();
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->fname} {$this->lname}");
    }
}