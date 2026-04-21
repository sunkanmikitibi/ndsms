<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name',
    'email',
    'password',
    'phone',
    'address',
    'town',
    'organization',
    'position',
    'department',
    'state',
    'country',
    'avatar',
    'is_super_admin',
])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

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
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get user's address indexing requests
     */
    public function addressIndexingRequests()
    {
        return $this->hasMany(AddressIndexingRequest::class);
    }

    /**
     * Get user's street revalidation requests
     */
    public function streetRevalidations()
    {
        return $this->hasMany(StreetRevalidation::class);
    }

    /**
     * Get user's payments
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get user's complaints
     */
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * Get user's street numbering plate requests
     */
    public function streetNumberingPlateRequests()
    {
        return $this->hasMany(StreetNumberingPlate::class);
    }
}
