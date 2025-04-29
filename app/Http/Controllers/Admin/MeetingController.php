<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Organization;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingController extends Controller
{
    /**
     * Display a listing of meetings.
     */
    public function index()
    {
        $meetings = Meeting::with(['room', 'users', 'createdBy'])
                          ->orderBy('start_time')
                          ->paginate(15);

        return view('admin.meetings.index', compact('meetings'));
    }

    /**
     * Show the form for creating a new meeting.
     */
    public function create()
    {
        $rooms = Room::all();
        $issuerOrgs = Organization::where('type', 'issuer')->get();
        $investorOrgs = Organization::where('type', 'investor')->get();

        // Get users for each organization type
        $issuers = User::whereIn('organization_id', $issuerOrgs->pluck('id'))->get();
        $investors = User::whereIn('organization_id', $investorOrgs->pluck('id'))->get();

        return view('admin.meetings.create', compact('rooms', 'issuers', 'investors'));
    }

    /**
     * Store a newly created meeting in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'issuer_id' => 'required|exists:users,id',
            'investor_ids' => 'required|array',
            'investor_ids.*' => 'exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'is_one_on_one' => 'boolean',
        ]);

        $meeting = Meeting::create([
            'room_id' => $validated['room_id'],
            'created_by_id' => Auth::id(), // Utilisateur actuellement connecté
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'is_one_on_one' => $validated['is_one_on_one'] ?? false,
        ]);

        // Add issuer as attendee
        $meeting->attendees()->create([
            'user_id' => $validated['issuer_id'],
            'role' => 'issuer',
        ]);

        // Add investors as attendees
        foreach ($validated['investor_ids'] as $investorId) {
            $meeting->attendees()->create([
                'user_id' => $investorId,
                'role' => 'investor',
            ]);
        }

        return redirect()->route('admin.meetings.index')
                         ->with('success', 'Meeting created successfully.');
    }

    /**
     * Display the specified meeting.
     */
    public function show(Meeting $meeting)
    {
        $meeting->load(['room', 'users', 'questions.user', 'createdBy']);

        return view('admin.meetings.show', compact('meeting'));
    }

    /**
     * Show the form for editing the specified meeting.
     */
    public function edit(Meeting $meeting)
    {
        $meeting->load('users');
        $rooms = Room::all();

        $issuerOrgs = Organization::where('type', 'issuer')->get();
        $investorOrgs = Organization::where('type', 'investor')->get();

        // Get users for each organization type
        $issuers = User::whereIn('organization_id', $issuerOrgs->pluck('id'))->get();
        $investors = User::whereIn('organization_id', $investorOrgs->pluck('id'))->get();

        $currentIssuer = $meeting->users()->wherePivot('role', 'issuer')->first();
        $currentInvestors = $meeting->users()->wherePivot('role', 'investor')->pluck('users.id')->toArray();

        return view('admin.meetings.edit', compact(
            'meeting',
            'rooms',
            'issuers',
            'investors',
            'currentIssuer',
            'currentInvestors'
        ));
    }

    /**
     * Update the specified meeting in storage.
     */
    public function update(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'issuer_id' => 'required|exists:users,id',
            'investor_ids' => 'required|array',
            'investor_ids.*' => 'exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'is_one_on_one' => 'boolean',
        ]);

        $meeting->update([
            'room_id' => $validated['room_id'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'is_one_on_one' => $validated['is_one_on_one'] ?? false,
        ]);

        // Remove all existing attendees
        $meeting->attendees()->delete();

        // Add issuer as attendee
        $meeting->attendees()->create([
            'user_id' => $validated['issuer_id'],
            'role' => 'issuer',
        ]);

        // Add investors as attendees
        foreach ($validated['investor_ids'] as $investorId) {
            $meeting->attendees()->create([
                'user_id' => $investorId,
                'role' => 'investor',
            ]);
        }

        return redirect()->route('admin.meetings.index')
                         ->with('success', 'Meeting updated successfully.');
    }

    /**
     * Remove the specified meeting from storage.
     */
    public function destroy(Meeting $meeting)
    {
        $meeting->delete();

        return redirect()->route('admin.meetings.index')
                         ->with('success', 'Meeting deleted successfully.');
    }
}
