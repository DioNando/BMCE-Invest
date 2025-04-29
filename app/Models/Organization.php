<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
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
        return $this->hasOne(User::class);
    }

    /**
     * Get meetings where this organization's users are participating.
     */
    public function meetings()
    {
        return $this->hasManyThrough(
            Meeting::class,
            MeetingAttendee::class,
            'user_id',
            'id',
            null,
            'meeting_id'
        )->whereHas('user', function ($query) {
            $query->where('organization_id', $this->id);
        });
    }
}
