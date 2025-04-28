<?php

namespace Database\Seeders;

use App\Models\Meeting;
use App\Models\MeetingAttendee;
use App\Models\Organization;
use App\Models\Question;
use App\Models\Room;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MeetingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = Room::all();
        $issuers = Organization::where('type', 'issuer')->get();
        $investors = Organization::where('type', 'investor')->get();

        // Conference dates: June 12-13, 2025
        $day1 = Carbon::create(2025, 6, 12, 9, 0, 0);
        $day2 = Carbon::create(2025, 6, 13, 9, 0, 0);

        // Create meetings for day 1
        $this->createDayMeetings($day1, $rooms, $issuers, $investors);

        // Create meetings for day 2
        $this->createDayMeetings($day2, $rooms, $issuers, $investors);
    }

    /**
     * Create meetings for a specific day.
     */
    private function createDayMeetings($dayStart, $rooms, $issuers, $investors)
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

                // 80% chance of creating a meeting in this slot
                if (rand(1, 100) <= 80) {
                    // Determine if this is a one-on-one meeting (20% chance)
                    $isOneOnOne = rand(1, 100) <= 20;

                    // Create meeting
                    $meeting = Meeting::create([
                        'room_id' => $room->id,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'is_one_on_one' => $isOneOnOne,
                    ]);

                    // Add issuer as attendee
                    MeetingAttendee::create([
                        'meeting_id' => $meeting->id,
                        'organization_id' => $issuer->id,
                        'role' => 'issuer',
                    ]);

                    // Add investors (1 for one-on-one, 2-4 for regular meetings)
                    // Limité par le nombre maximum d'investisseurs disponibles
                    $investorCount = $isOneOnOne ? 1 : rand(2, min(4, $investors->count()));
                    $meetingInvestors = $investors->random($investorCount);

                    foreach ($meetingInvestors as $investor) {
                        MeetingAttendee::create([
                            'meeting_id' => $meeting->id,
                            'organization_id' => $investor->id,
                            'role' => 'investor',
                        ]);

                        // 50% chance of adding a question from this investor
                        if (rand(1, 100) <= 50) {
                            Question::create([
                                'meeting_id' => $meeting->id,
                                'user_id' => $investor->user->id,
                                'question' => $this->getRandomQuestion(),
                                'is_answered' => false,
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Get a random sample question.
     */
    private function getRandomQuestion()
    {
        $questions = [
            'What are your growth projections for the next fiscal year?',
            'Can you elaborate on your dividend policy?',
            'What are the major risks facing your business currently?',
            'How do you plan to address the challenges in your industry?',
            'What is your strategy for international expansion?',
            'Could you discuss your ESG initiatives?',
            'How is the current economic climate affecting your operations?',
            'What competitive advantages do you have over your peers?',
            'What are your capital allocation priorities?',
            'Could you provide more details about your recent restructuring efforts?',
        ];

        return $questions[array_rand($questions)];
    }
}
