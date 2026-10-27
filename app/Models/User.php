<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
            'role' => 'string',
        ];
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'content_manager', 'store_manager', 'sales_manager'], true);
    }

    public function canManageContent(): bool
    {
        return in_array($this->role, ['super_admin', 'content_manager'], true);
    }

    public function canManageStore(): bool
    {
        return in_array($this->role, ['super_admin', 'store_manager'], true);
    }

    public function canManageSales(): bool
    {
        return in_array($this->role, ['super_admin', 'sales_manager'], true);
    }
}
