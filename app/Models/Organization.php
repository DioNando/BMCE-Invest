<?php

namespace App\Models;

use App\Enums\OrganizationType;
use App\Enums\Origin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\Cast;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'origin',
        'organization_type',
        'organization_other',
        'profil',
        'fiche_bkgr',
        'country_id',
        'description',
    ];

    protected $casts = [
        'origin' => Origin::class,
        'organization_type' => OrganizationType::class,
    ];

    /**
     * Get the user that owns the organization.
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    /**
     * Get the country that this organization belongs to.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get time slots where this organization's users are participating.
     */
    public function timeSlots()
    {
        return $this->hasManyThrough(
            TimeSlot::class,
            TimeSlotAttendee::class,
            'user_id',
            'id',
            null,
            'time_slot_id'
        )->whereHas('user', function ($query) {
            $query->where('organization_id', $this->id);
        });
    }
}
