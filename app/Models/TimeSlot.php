<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'created_by_id',
        'start_time',
        'end_time',
        'is_one_on_one',
        'availability',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_one_on_one' => 'boolean',
        'availability' => 'boolean',
    ];

    /**
     * Get the room for this timeslot.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the user who created this timeslot.
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
        return $this->hasMany(TimeSlotAttendee::class);
    }

    /**
     * Get the users attending this timeslot.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'time_slot_attendees')
                    ->withTimestamps();
    }

    /**
     * Get the issuer user for this timeslot.
     */
    public function issuer()
    {
        return $this->attendees()->whereNotNull('issuer_id')
                   ->with('issuer')
                   ->first()?->issuer;
    }

    /**
     * Get the investor users for this timeslot.
     */
    public function investors()
    {
        $attendeeRecords = $this->attendees()->whereNotNull('investor_id')->get();
        return $attendeeRecords->map(function($record) {
            return $record->investor;
        });
    }

    /**
     * Get the questions for this timeslot.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
