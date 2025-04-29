<?php

namespace App\Http\Controllers\Investor;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the investor dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->hasRole(UserRole::INVESTOR->value)) {
            return redirect()->route('home')
                             ->with('error', 'You do not have permission to access this page.');
        }

        $organization = $user->organization;

        // Récupérer les créneaux horaires où l'utilisateur participe en tant qu'investisseur
        $timeSlots = $user->timeSlots()
                        ->where('time_slot_attendees.investor_id', $user->id)
                        ->with(['room', 'users', 'questions' => function($query) use ($user) {
                            $query->where('user_id', $user->id);
                        }])
                        ->orderBy('start_time')
                        ->get();

        // Group timeSlots by date for easier display
        $timeSlotsByDate = $timeSlots->groupBy(function($timeSlot) {
            return $timeSlot->start_time->format('Y-m-d');
        });

        return view('investor.dashboard', compact('user', 'organization', 'timeSlotsByDate'));
    }
}
