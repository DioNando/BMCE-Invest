<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\TimeSlot;
use App\Models\TimeSlotAttendee;
use App\Models\Organization;
use App\Models\Question;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = Room::all();
        $issuerOrgs = Organization::where('organization_type', 'issuer')->get();
        $investorOrgs = Organization::where('organization_type', 'investor')->get();

        // Récupérer les utilisateurs liés aux organisations
        $issuers = User::whereIn('organization_id', $issuerOrgs->pluck('id'))->get();
        $investors = User::whereIn('organization_id', $investorOrgs->pluck('id'))->get();

        // Conference dates: June 12-13, 2025
        $day1 = Carbon::create(2025, 6, 12, 9, 0, 0);
        $day2 = Carbon::create(2025, 6, 13, 9, 0, 0);

        // Create time slots for day 1
        $this->createDayTimeSlots($day1, $rooms, $issuers, $investors);

        // Create time slots for day 2
        $this->createDayTimeSlots($day2, $rooms, $issuers, $investors);
    }

    /**
     * Create time slots for a specific day.
     */
    private function createDayTimeSlots($dayStart, $rooms, $issuers, $investors)
    {
        $slotDuration = 45; // minutes
        $slotsPerDay = 8; // 9:00 to 17:00 with 45-minute slots

        // Calcul du nombre maximum d'investisseurs qu'on peut sélectionner
        $maxInvestors = min(4, $investors->count());

        foreach ($issuers as $issuer) {
            // Assign a random room for this issuer for the day
            $room = $rooms->random();

            for ($slot = 0; $slot < $slotsPerDay; $slot++) {
                $startTime = (clone $dayStart)->addMinutes($slot * $slotDuration);
                $endTime = (clone $startTime)->addMinutes($slotDuration);

                // Create time slot with 80% availability
                $isAvailable = rand(1, 100) <= 80;

                // Determine if this is a one-on-one slot (20% chance)
                $isOneOnOne = rand(1, 100) <= 20;

                // Create time slot
                $timeSlot = TimeSlot::create([
                    'room_id' => $room->id,
                    'created_by_id' => $issuer->id,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'is_one_on_one' => $isOneOnOne,
                    'availability' => $isAvailable,
                ]);

                // Add issuer as attendee
                TimeSlotAttendee::create([
                    'time_slot_id' => $timeSlot->id,
                    'user_id' => $issuer->id,
                    'issuer_id' => $issuer->id,
                    'status' => Status::CONFIRMED,
                ]);

                // Only add investors if the slot is available
                if ($isAvailable) {
                    // Add investors (1 for one-on-one, 2-4 for regular slots)
                    $investorCount = $isOneOnOne ? 1 : rand(2, $maxInvestors);
                    $slotInvestors = $investors->random($investorCount);

                    foreach ($slotInvestors as $investor) {
                        TimeSlotAttendee::create([
                            'time_slot_id' => $timeSlot->id,
                            'user_id' => $investor->id,
                            'investor_id' => $investor->id,
                            'status' => Status::CONFIRMED,
                        ]);

                        // 50% chance of adding a question from this investor
                        if (rand(1, 100) <= 50) {
                            Question::create([
                                'time_slot_id' => $timeSlot->id,
                                'user_id' => $investor->id,
                                'question' => 'Question de ' . $investor->name . ' pour ' . $issuer->name . '?',
                                'is_answered' => false,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
