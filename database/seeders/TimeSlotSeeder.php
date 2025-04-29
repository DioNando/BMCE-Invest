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
use Illuminate\Support\Facades\Log;

class TimeSlotSeeder extends Seeder
{
    /**
     * Liste de questions prédéfinies pertinentes pour les rencontres investisseurs-émetteurs
     */
    private $questionsList = [
        "What are your growth plans for the next 3 years?",
        "How do you plan to finance your international development?",
        "What are your operational margins by market segment?",
        "What is your strategy regarding new entrants in your sector?",
        "What is your action plan to reduce your carbon footprint?",
        "How are you approaching the digital transformation of your company?",
        "What are your main operational risks and how do you manage them?",
        "What acquisitions are you considering in the next 12 months?",
        "What is your medium-term dividend policy?",
        "How is your governance adapting to new ESG requirements?",
        "What are your R&D investments and their expected returns?",
        "What is the impact of inflation on your cost structure?",
        "How are you managing supply chain tensions?",
        "What is your currency risk hedging strategy?",
        "How do you evaluate the current valuation of your company?",
        "What are your main competitive advantages in the market?",
        "How do you anticipate regulatory changes in your sector?",
        "What synergies do you expect from your recent acquisitions?",
        "What is your 5-year technology roadmap?",
        "How do you attract and retain key talent?",
    ];

    // Compteur pour les statistiques
    private $totalQuestions = 0;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = Room::all();

        // Récupérer les organisations correctement en filtrant par le champ 'profil'
        $issuerOrgs = Organization::where('profil', UserRole::ISSUER->value)->get();
        $investorOrgs = Organization::where('profil', UserRole::INVESTOR->value)->get();

        // Récupérer les utilisateurs liés aux organisations
        $issuers = User::whereIn('organization_id', $issuerOrgs->pluck('id'))->get();
        $investors = User::whereIn('organization_id', $investorOrgs->pluck('id'))->get();

        // Vérifier que nous avons des données
        if ($issuers->isEmpty() || $investors->isEmpty() || $rooms->isEmpty()) {
            echo "Avertissement: Données manquantes pour créer des créneaux horaires.\n";
            echo "Issuers: " . $issuers->count() . ", Investors: " . $investors->count() . ", Rooms: " . $rooms->count() . "\n";
            return;
        }

        echo "Création des créneaux pour " . $issuers->count() . " émetteurs et " . $investors->count() . " investisseurs.\n";

        // Conference dates: June 12-13, 2025
        $day1 = Carbon::create(2025, 6, 12, 9, 0, 0);
        $day2 = Carbon::create(2025, 6, 13, 9, 0, 0);

        // Create time slots for day 1
        $this->createDayTimeSlots($day1, $rooms, $issuers, $investors);

        // Create time slots for day 2
        $this->createDayTimeSlots($day2, $rooms, $issuers, $investors);

        echo "Total questions créées: " . $this->totalQuestions . "\n";
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

                // Les émetteurs sont toujours confirmés
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

                    // S'assurer qu'au moins un investisseur est confirmé pour avoir des questions
                    $hasConfirmedInvestor = false;

                    foreach ($slotInvestors as $investor) {
                        // Répartition aléatoire des statuts: 60% confirmés, 30% en attente, 10% refusés
                        $statusRandom = rand(1, 100);
                        $status = match(true) {
                            $statusRandom <= 60 => Status::CONFIRMED,
                            $statusRandom <= 90 => Status::PENDING,
                            default => Status::REFUSED,
                        };

                        // Forcer au moins un investisseur à être confirmé
                        if (!$hasConfirmedInvestor && $investor === $slotInvestors->last()) {
                            $status = Status::CONFIRMED;
                            $hasConfirmedInvestor = true;
                        } elseif ($status === Status::CONFIRMED) {
                            $hasConfirmedInvestor = true;
                        }

                        TimeSlotAttendee::create([
                            'time_slot_id' => $timeSlot->id,
                            'user_id' => $investor->id,
                            'investor_id' => $investor->id,
                            'status' => $status,
                        ]);
                    }

                    // Ajouter une question pour chaque investisseur confirmé ou en attente (avec 80% de chance)
                    foreach ($slotInvestors as $investor) {
                        $attendee = TimeSlotAttendee::where('time_slot_id', $timeSlot->id)
                            ->where('user_id', $investor->id)
                            ->first();

                        if (!$attendee) {
                            continue;
                        }

                        $attendeeStatus = $attendee->status;

                        // Vérifier si le statut est CONFIRMED ou PENDING (soit en comparant la valeur, soit l'objet)
                        $isConfirmedOrPending = $attendeeStatus === Status::CONFIRMED->value ||
                                               $attendeeStatus === Status::PENDING->value ||
                                               $attendeeStatus === Status::CONFIRMED ||
                                               $attendeeStatus === Status::PENDING;

                        // 80% de chance d'avoir une question
                        $shouldAskQuestion = rand(1, 100) <= 80;

                        if ($isConfirmedOrPending && $shouldAskQuestion) {
                            // 80% de chances que la question soit répondue si l'investisseur est confirmé
                            $isAnswered = false;

                            if ($attendeeStatus === Status::CONFIRMED->value || $attendeeStatus === Status::CONFIRMED) {
                                $isAnswered = rand(1, 100) <= 80;
                            }

                            // Sélectionner une question aléatoire de la liste
                            $randomQuestion = $this->questionsList[array_rand($this->questionsList)];

                            try {
                                $question = Question::create([
                                    'time_slot_id' => $timeSlot->id,
                                    'user_id' => $investor->id,
                                    'question' => $randomQuestion,
                                    'is_answered' => $isAnswered,
                                ]);

                                $this->totalQuestions++;
                            } catch (\Exception $e) {
                                echo "Erreur lors de la création d'une question: " . $e->getMessage() . "\n";
                            }
                        }
                    }
                }
            }
        }
    }
}
