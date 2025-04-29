<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\TimeSlot;
use App\Models\TimeSlotAttendee;
use App\Models\User;
use Illuminate\Http\Request;

class TimeSlotController extends Controller
{
    /**
     * Display a listing of the time slots.
     */
    public function index()
    {
        $timeSlots = TimeSlot::with(['room', 'createdBy', 'attendees.user'])->get();
        return view('admin.time-slots.index', compact('timeSlots'));
    }

    /**
     * Show the form for creating a new time slot.
     */
    public function create()
    {
        $rooms = Room::all();
        $users = User::all();
        return view('admin.time-slots.create', compact('rooms', 'users'));
    }

    /**
     * Store a newly created time slot in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'created_by_id' => 'required|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'is_one_on_one' => 'boolean',
            'availability' => 'boolean',
            'attendees' => 'array',
        ]);

        $timeSlot = TimeSlot::create($validatedData);

        // Handle attendees if present
        if (!empty($request->attendees)) {
            foreach ($request->attendees as $attendeeData) {
                $attendeeRecord = [
                    'time_slot_id' => $timeSlot->id,
                    'user_id' => $attendeeData['user_id'],
                    'status' => Status::PENDING,
                ];

                // Déterminer le rôle et ajouter l'ID correspondant
                if (isset($attendeeData['role']) && $attendeeData['role'] === 'issuer') {
                    $attendeeRecord['issuer_id'] = $attendeeData['user_id'];
                } elseif (isset($attendeeData['role']) && $attendeeData['role'] === 'investor') {
                    $attendeeRecord['investor_id'] = $attendeeData['user_id'];
                }

                TimeSlotAttendee::create($attendeeRecord);
            }
        }

        return redirect()->route('admin.time-slots.index')
            ->with('success', 'Time slot created successfully.');
    }

    /**
     * Display the specified time slot.
     */
    public function show(TimeSlot $timeSlot)
    {
        $timeSlot->load(['room', 'createdBy', 'attendees.user', 'questions']);
        return view('admin.time-slots.show', compact('timeSlot'));
    }

    /**
     * Show the form for editing the specified time slot.
     */
    public function edit(TimeSlot $timeSlot)
    {
        $rooms = Room::all();
        $users = User::all();
        $timeSlot->load('attendees.user');
        return view('admin.time-slots.edit', compact('timeSlot', 'rooms', 'users'));
    }

    /**
     * Update the specified time slot in storage.
     */
    public function update(Request $request, TimeSlot $timeSlot)
    {
        $validatedData = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'created_by_id' => 'required|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'is_one_on_one' => 'boolean',
            'availability' => 'boolean',
            'attendees' => 'array',
        ]);

        $timeSlot->update($validatedData);

        // Handle attendees
        if (isset($request->attendees)) {
            // Remove existing attendees
            TimeSlotAttendee::where('time_slot_id', $timeSlot->id)->delete();

            // Add new attendees
            foreach ($request->attendees as $attendeeData) {
                $attendeeRecord = [
                    'time_slot_id' => $timeSlot->id,
                    'user_id' => $attendeeData['user_id'],
                    'status' => Status::PENDING,
                ];

                // Déterminer le rôle et ajouter l'ID correspondant
                if (isset($attendeeData['role']) && $attendeeData['role'] === 'issuer') {
                    $attendeeRecord['issuer_id'] = $attendeeData['user_id'];
                } elseif (isset($attendeeData['role']) && $attendeeData['role'] === 'investor') {
                    $attendeeRecord['investor_id'] = $attendeeData['user_id'];
                }

                TimeSlotAttendee::create($attendeeRecord);
            }
        }

        return redirect()->route('admin.time-slots.index')
            ->with('success', 'Time slot updated successfully.');
    }

    /**
     * Remove the specified time slot from storage.
     */
    public function destroy(TimeSlot $timeSlot)
    {
        $timeSlot->delete();

        return redirect()->route('admin.time-slots.index')
            ->with('success', 'Time slot deleted successfully.');
    }

    /**
     * Toggle the availability status of a time slot.
     */
    public function toggleAvailability(TimeSlot $timeSlot)
    {
        $timeSlot->availability = !$timeSlot->availability;
        $timeSlot->save();

        return redirect()->back()
            ->with('success', 'Time slot availability updated successfully.');
    }
}
