<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    // use HasApiTokens, HasFactory, Notifiable, HasRoles;
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'position',
        'profile_completed',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'profile_completed' => 'boolean',
    ];

    /**
     * Get the organization associated with the user.
     */
    public function organization()
    {
        return $this->hasOne(Organization::class);
    }

    /**
     * Get the questions asked by this user.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Check if user is from an investor organization.
     */
    public function isInvestor(): bool
    {
        return $this->organization && $this->organization->type === 'investor';
    }

    /**
     * Check if user is from an issuer organization.
     */
    public function isIssuer(): bool
    {
        return $this->organization && $this->organization->type === 'issuer';
    }
}
