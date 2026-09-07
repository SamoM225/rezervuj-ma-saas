<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use BelongsToTenant, HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'city',
        'role',
        'city_id',
        'calendar_color',
        'avatar_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
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
            'password' => 'hashed',
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Whether the user has confirmed two-factor authentication enabled.
     */
    public function hasTwoFactorEnabled(): bool
    {
        return ! empty($this->two_factor_secret) && $this->two_factor_confirmed_at !== null;
    }

    public function schedules()
    {
        return $this->belongsToMany(Schedule::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    public function availability()
    {
        return $this->hasMany(WorkerAvailability::class, 'user_id');
    }

    public function getIsAdminAttribute()
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }

    public function getIsSuperAdminAttribute()
    {
        return $this->role === 'superadmin';
    }

    public function getIsWorkerAttribute()
    {
        return $this->role === 'worker';
    }

    // Role checking methods
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function hasAnyRole($roles)
    {
        return in_array($this->role, $roles);
    }

    public function canManageRoles()
    {
        return $this->role === 'superadmin';
    }

    public function canManageWorkers()
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }

    public function canManageCategories()
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }

    public function canViewAllBookings()
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }

    public function canManageBusinessSettings()
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }

    public function canManageWorkerAvailability()
    {
        return $this->role === 'superadmin';
    }

    // Check if worker is available at specific date and time
    public function isAvailableAt($date, $time)
    {
        return $this->availability()
            ->where('is_active', true)
            ->get()
            ->some(function ($availability) use ($date, $time) {
                return $availability->isAvailableAt($date, $time);
            });
    }
}
