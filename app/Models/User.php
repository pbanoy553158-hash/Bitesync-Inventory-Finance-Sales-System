<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_photo_path',
        'theme',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Keep role checks consistent when older records use different casing
     * or include formatting such as square brackets.
     */
    public function getRoleAttribute(?string $value): ?string
    {
        $normalized = strtoupper(trim((string) $value, " \t\n\r\0\x0B[]"));

        return match ($normalized) {
            'CEO/ADMIN', 'ADMIN' => 'CEO/Admin',
            'FINANCE' => 'Finance',
            'PROCUREMENT' => 'Procurement',
            default => $value,
        };
    }

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
}