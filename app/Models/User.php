<?php

namespace App\Models;

use App\Enums\UserRole;
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
        'organization_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean',
        'role' => UserRole::class,
    ];

    /**
     * Get the organization associated with the user.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the questions asked by this user.
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'user_id');
    }

    /**
     * Get the meeting attendances for the user.
     */
    public function meetingAttendances()
    {
        return $this->hasMany(MeetingAttendee::class);
    }

    /**
     * Get the meetings this user is attending.
     */
    public function meetings()
    {
        return $this->belongsToMany(Meeting::class, 'meeting_attendees')
                    ->withPivot('role')
                    ->withTimestamps();
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
