<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'organization_type',
        'country',
        'description',
    ];

    /**
     * Get the user that owns the organization.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the meeting attendances for the organization.
     */
    public function meetingAttendances()
    {
        return $this->hasMany(MeetingAttendee::class);
    }

    /**
     * Get the meetings this organization is attending.
     */
    public function meetings()
    {
        return $this->belongsToMany(Meeting::class, 'meeting_attendees')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Get meetings where this organization is an issuer.
     */
    public function issuerMeetings()
    {
        return $this->meetings()->wherePivot('role', 'issuer');
    }

    /**
     * Get meetings where this organization is an investor.
     */
    public function investorMeetings()
    {
        return $this->meetings()->wherePivot('role', 'investor');
    }
}
