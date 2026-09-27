<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const APPROVAL_PENDING = 'pending';

    public const APPROVAL_APPROVED = 'approved';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'approval_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'CEO/Admin';
    }

    public function isFinance(): bool
    {
        return $this->role === 'Finance';
    }

    public function isProcurement(): bool
    {
        return $this->role === 'Procurement';
    }

    public function isRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isApproved(): bool
    {
        return $this->approval_status === self::APPROVAL_APPROVED;
    }
}