<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
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
        $organization = $user->organization;

        if (!$organization || $organization->type !== 'investor') {
            return redirect()->route('home')
                             ->with('error', 'You do not have permission to access this page.');
        }

        $meetings = $organization->investorMeetings()
                                ->with(['room', 'organizations', 'questions' => function($query) use ($user) {
                                    $query->where('user_id', $user->id);
                                }])
                                ->orderBy('start_time')
                                ->get();

        // Group meetings by date for easier display
        $meetingsByDate = $meetings->groupBy(function($meeting) {
            return $meeting->start_time->format('Y-m-d');
        });

        return view('investor.dashboard', compact('organization', 'meetingsByDate'));
    }
}
