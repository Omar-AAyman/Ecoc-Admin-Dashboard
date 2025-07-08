<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role_id',
        'company_id',
        'phone',
        'image',
        'status',
        'position',
        'reactivated_at',
    ];

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
    protected $casts = [
        'deleted_at' => 'datetime',
        'reactivated_at' => 'datetime',
        'status' => 'string',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function tankRentals()
    {
        return $this->hasMany(TankRental::class);
    }

    public function transactionDocuments()
    {
        return $this->hasMany(TransactionDocument::class, 'uploaded_by');
    }

    public function isSuperAdmin()
    {
        return $this->role->name === 'super_admin';
    }

    public function isCEO()
    {
        return $this->role->name === 'ceo';
    }

    public function isClient()
    {
        return $this->role->name === 'client';
    }
    public function hasRole($role)
    {
        return $this->role && $this->role->name === $role;
    }

    public function hasAnyRole($roles)
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        return $this->role && in_array($this->role->name, $roles);
    }

    public function transactionsAsEngineer()
    {
        return $this->hasMany(Transaction::class, 'engineer_id');
    }

    public function transactionsAsTechnician()
    {
        return $this->hasMany(Transaction::class, 'technician_id');
    }


    public function getImageUrlAttribute(): string
    {
        return $this->image ? asset('storage/' . $this->image) : asset('panel/assets/img/users/1.jpg');
    }
}
