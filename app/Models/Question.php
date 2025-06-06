<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'time_slot_id',
        'user_id',
        'question',
        'is_answered',
    ];

    protected $casts = [
        'is_answered' => 'boolean',
    ];

    /**
     * Get the time slot this question belongs to.
     */
    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    /**
     * Get the user who asked this question.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
