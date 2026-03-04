<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
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
        'role',
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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determine if the user can access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Admin Dashboard (rbdashboard) - Only Super Admins and Admins
        if ($panel->getId() === 'rbdashboard') {
            return $this->isSuperAdmin() || $this->isAdmin();
        }

        // Client Area (client) - Everyone can access this (or specifically premium/regular + impersonators)
        if ($panel->getId() === 'client') {
            return true;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | RBAC Helpers
    |--------------------------------------------------------------------------
    */

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPremium(): bool
    {
        return $this->role === 'premium_user';
    }

    public function isRegular(): bool
    {
        return $this->role === 'regular_user';
    }

    /*
    |--------------------------------------------------------------------------
    | Impersonation Helper
    |--------------------------------------------------------------------------
    */

    public function canImpersonate(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }
}
