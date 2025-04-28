<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Organization;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalUsers' => User::count(),
            'totalOrganizations' => Organization::count(),
            'issuers' => Organization::where('type', 'issuer')->count(),
            'investors' => Organization::where('type', 'investor')->count(),
            'meetings' => Meeting::count(),
            'rooms' => Room::count(),
        ];

        $recentMeetings = Meeting::with(['room', 'organizations'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentMeetings'));
    }
}
