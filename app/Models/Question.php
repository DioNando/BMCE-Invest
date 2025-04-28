<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'user_id',
        'question',
        'is_answered',
    ];

    protected $casts = [
        'is_answered' => 'boolean',
    ];

    /**
     * Get the meeting this question belongs to.
     */
    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    /**
     * Get the user who asked this question.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
