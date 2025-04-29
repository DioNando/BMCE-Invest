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

        // Récupérer les réunions où l'utilisateur participe en tant qu'émetteur
        $meetings = $user->meetings()
                        ->wherePivot('role', 'issuer')
                        ->with(['room', 'users', 'questions.user'])
                        ->orderBy('start_time')
                        ->get();

        // Group meetings by date for easier display
        $meetingsByDate = $meetings->groupBy(function($meeting) {
            return $meeting->start_time->format('Y-m-d');
        });

        return view('issuer.dashboard', compact('user', 'organization', 'meetingsByDate'));
    }
}
