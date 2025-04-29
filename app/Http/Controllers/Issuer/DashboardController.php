<?php

namespace App\Http\Controllers\Issuer;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the issuer dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->hasRole(UserRole::ISSUER->value)) {
            return redirect()->route('home')
                             ->with('error', 'You do not have permission to access this page.');
        }

        $organization = $user->organization;

        // Récupérer les créneaux horaires où l'utilisateur participe en tant qu'émetteur
        $timeSlots = $user->timeSlots()
                        ->wherePivot('role', 'issuer')
                        ->with(['room', 'users', 'questions.user'])
                        ->orderBy('start_time')
                        ->get();

        // Regrouper les créneaux horaires par date pour un affichage plus facile
        $timeSlotsByDate = $timeSlots->groupBy(function($timeSlot) {
            return $timeSlot->start_time->format('Y-m-d');
        });

        return view('issuer.dashboard', compact('user', 'organization', 'timeSlotsByDate', 'timeSlots'));
    }
}
