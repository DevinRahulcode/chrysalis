<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subsidiaries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SubsidiariesController extends Controller
{
    /**
     * Display a listing of the subsidiaries.
     */
    public function index()
    {
        $subsidiaries = Subsidiaries::all();
        return view('subsidiaries.index', compact('subsidiaries'));
    }

    /**
     * Show the form for creating a new subsidiary.
     */
    public function create()
    {
        return view('subsidiaries.create');
    }

    /**
     * Store a newly created subsidiary in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subsidiaries_hero_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'subsidiaries_title' => 'required|string|max:255',
            'subsidiaries_description' => 'nullable|string',
            'status' => 'required|in:Y,N',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'subsidiaries_title' => $request->subsidiaries_title,
            'subsidiaries_description' => $request->subsidiaries_description,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ];

        // Handle image upload
        if ($request->hasFile('subsidiaries_hero_image')) {
            $imagePath = $request->file('subsidiaries_hero_image')->store('subsidiaries', 'public');
            $data['subsidiaries_hero_image'] = $imagePath;
        }

        Subsidiaries::create($data);

        return redirect()->route('subsidiaries.index')->with('success', 'Subsidiary created successfully.');
    }

    /**
     * Display the specified subsidiary.
     */
    public function show(Subsidiaries $subsidiary)
    {
        return view('subsidiaries.show', compact('subsidiary'));
    }

    /**
     * Show the form for editing the specified subsidiary.
     */
    public function edit(Subsidiaries $subsidiary)
    {
        return view('subsidiaries.edit', compact('subsidiary'));
    }

    /**
     * Update the specified subsidiary in storage.
     */
    public function update(Request $request, Subsidiaries $subsidiary)
    {
        $validator = Validator::make($request->all(), [
            'subsidiaries_hero_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'subsidiaries_title' => 'required|string|max:255',
            'subsidiaries_description' => 'nullable|string',
            'status' => 'required|in:Y,N',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'subsidiaries_title' => $request->subsidiaries_title,
            'subsidiaries_description' => $request->subsidiaries_description,
            'status' => $request->status,
            'updated_by' => Auth::id(),
        ];

        // Handle image upload
        if ($request->hasFile('subsidiaries_hero_image')) {
            // Delete old image if it exists
            if ($subsidiary->subsidiaries_hero_image) {
                Storage::disk('public')->delete($subsidiary->subsidiaries_hero_image);
            }
            $imagePath = $request->file('subsidiaries_hero_image')->store('subsidiaries', 'public');
            $data['subsidiaries_hero_image'] = $imagePath;
        }

        $subsidiary->update($data);

        return redirect()->route('subsidiaries.index')->with('success', 'Subsidiary updated successfully.');
    }

    /**
     * Remove the specified subsidiary from storage (soft delete).
     */
    public function destroy(Subsidiaries $subsidiary)
    {
        $subsidiary->update([
            'deleted_by' => Auth::id(),
        ]);
        $subsidiary->delete();

        return redirect()->route('subsidiaries.index')->with('success', 'Subsidiary deleted successfully.');
    }
}