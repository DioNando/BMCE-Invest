<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingAttendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'organization_id',
        'role',
    ];

    /**
     * Get the meeting that this attendance record belongs to.
     */
    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    /**
     * Get the organization associated with this attendance.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
