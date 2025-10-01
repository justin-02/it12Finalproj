<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];
    
    public function isAdmin() { return $this->role === 'admin'; }
    public function isInventory() { return $this->role === 'inventory'; }
    public function isCashier() { return $this->role === 'cashier'; }
    public function isHelper() { return $this->role === 'helper'; }
}
