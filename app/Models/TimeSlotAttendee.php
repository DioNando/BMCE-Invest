<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlotAttendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'time_slot_id',
        'user_id',
        'investor_id',
        'issuer_id',
        'status',
    ];

    protected $casts = [
        'status' => Status::class,
    ];

    /**
     * Get the time slot that this attendance record belongs to.
     */
    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    /**
     * Get the user associated with this attendance.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
