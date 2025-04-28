<?php

namespace App\Http\Controllers\Issuer;

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
        $organization = $user->organization;

        if (!$organization || $organization->type !== 'issuer') {
            return redirect()->route('home')
                             ->with('error', 'You do not have permission to access this page.');
        }

        $meetings = $organization->issuerMeetings()
                                ->with(['room', 'organizations', 'questions.user'])
                                ->orderBy('start_time')
                                ->get();

        // Group meetings by date for easier display
        $meetingsByDate = $meetings->groupBy(function($meeting) {
            return $meeting->start_time->format('Y-m-d');
        });

        return view('issuer.dashboard', compact('organization', 'meetingsByDate'));
    }
}
