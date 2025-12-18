<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'hire_date',
        'position',
        'department',
        'is_active',
        'permissions',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
        'last_logout_at' => 'datetime',
        'last_seen' => 'datetime',
        'hire_date' => 'date',
        'permissions' => 'array',
    ];

    public function sales()
    {
        return $this->hasMany(Order::class, 'cashier_id');
    }

    public function sessionTracks()
    {
        return $this->hasMany(\App\Models\SessionTrack::class);
    }

    public function tasks()
    {
        return $this->hasMany(\App\Models\Task::class);
    }

    public function productivityMetrics()
    {
        return $this->hasMany(\App\Models\ProductivityMetric::class);
    }

    public function employeeLogs()
    {
        return $this->hasMany(EmployeeLog::class);
    }

    public function latestEmployeeLog()
    {
        return $this->hasOne(EmployeeLog::class)->latestOfMany();
    }

    public function batches()
    {
        return $this->belongsToMany(Batch::class, 'batch_products')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = Arr::wrap($roles);
        return in_array($this->role, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isInventory(): bool
    {
        return $this->hasRole('inventory');
    }

    public function isCashier(): bool
    {
        return $this->hasRole('cashier');
    }

    public function isSales(): bool
    {
        return $this->hasRole('sales');
    }

    public function isHelper(): bool
    {
        return $this->hasRole('helper');
    }
}