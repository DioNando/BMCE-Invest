<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Organization;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->hasRole(UserRole::ADMIN->value)) {
            return redirect()->route('home')
                             ->with('error', 'You do not have permission to access this page.');
        }

        // Statistiques basées sur les utilisateurs et les rôles
        $stats = [
            'totalUsers' => User::count(),
            'totalOrganizations' => Organization::count(),
            'issuers' => User::role(UserRole::ISSUER->value)->count(),
            'investors' => User::role(UserRole::INVESTOR->value)->count(),
            'meetings' => Meeting::count(),
            'rooms' => Room::count(),
        ];

        $recentMeetings = Meeting::with(['room', 'users.organization'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentMeetings'));
    }
}
