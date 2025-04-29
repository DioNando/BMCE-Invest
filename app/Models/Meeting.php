<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'created_by_id',
        'start_time',
        'end_time',
        'is_one_on_one',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_one_on_one' => 'boolean',
    ];

    /**
     * Get the room for this meeting.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the user who created this meeting.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Get the attendees records.
     */
    public function attendees()
    {
        return $this->hasMany(MeetingAttendee::class);
    }

    /**
     * Get the users attending this meeting.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'meeting_attendees')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Get the issuer user for this meeting.
     */
    public function issuer()
    {
        return $this->users()->wherePivot('role', 'issuer')->first();
    }

    /**
     * Get the investor users for this meeting.
     */
    public function investors()
    {
        return $this->users()->wherePivot('role', 'investor')->get();
    }

    /**
     * Get the questions for this meeting.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
