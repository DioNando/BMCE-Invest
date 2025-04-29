<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Country;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organizations = Organization::with('country')->paginate(10);
        return view('admin.organizations.index', compact('organizations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = Country::all();
        return view('admin.organizations.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'origin' => 'required|in:national,foreign',
            'profil' => 'required|in:issuer,investor',
            'organization_type' => 'nullable|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'description' => 'nullable|string',
        ]);

        $organization = Organization::create($validated);

        return redirect()->route('admin.organizations.index')
                        ->with('success', 'Organization created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization)
    {
        $organization->load('country');
        return view('admin.organizations.show', compact('organization'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization)
    {
        $countries = Country::all();
        return view('admin.organizations.edit', compact('organization', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'origin' => 'required|in:national,foreign',
            'profil' => 'required|in:issuer,investor',
            'organization_type' => 'nullable|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'description' => 'nullable|string',
        ]);

        $organization->update($validated);

        return redirect()->route('admin.organizations.index')
                        ->with('success', 'Organization updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization)
    {
        $organization->delete();

        return redirect()->route('admin.organizations.index')
                        ->with('success', 'Organization deleted successfully.');
    }
}
