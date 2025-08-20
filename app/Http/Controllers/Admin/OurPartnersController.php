<?php

namespace App\Http\Controllers;

use App\Models\OurPartners;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class OurPartnersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $partners = OurPartners::with('homePage')->get();
        return view('our_partners.index', compact('partners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('our_partners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'home_id' => 'required|exists:home_page,id',
            'partner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'partner_title' => 'nullable|string|max:255',
            'partner_description' => 'nullable|string',
            'status' => 'required|in:Y,N',
        ]);

        $data = $validated;
        $data['created_by'] = Auth::id();

        if ($request->hasFile('partner_image')) {
            $path = $request->file('partner_image')->store('partner_images', 'public');
            $data['partner_image'] = $path;
        }

        OurPartners::create($data);

        return redirect()->route('our-partners.index')->with('success', 'Partner created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OurPartners $ourPartner)
    {
        return view('our_partners.show', compact('ourPartner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OurPartners $ourPartner)
    {
        return view('our_partners.edit', compact('ourPartner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OurPartners $ourPartner)
    {
        $validated = $request->validate([
            'home_id' => 'required|exists:home_page,id',
            'partner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'partner_title' => 'nullable|string|max:255',
            'partner_description' => 'nullable|string',
            'status' => 'required|in:Y,N',
        ]);

        $data = $validated;
        $data['updated_by'] = Auth::id();

        if ($request->hasFile('partner_image')) {
            // Delete old image if it exists
            if ($ourPartner->partner_image) {
                Storage::disk('public')->delete($ourPartner->partner_image);
            }
            $path = $request->file('partner_image')->store('partner_images', 'public');
            $data['partner_image'] = $path;
        }

        $ourPartner->update($data);

        return redirect()->route('our-partners.index')->with('success', 'Partner updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OurPartners $ourPartner)
    {
        $ourPartner->deleted_by = Auth::id();
        $ourPartner->save();

        // Delete the associated image
        if ($ourPartner->partner_image) {
            Storage::disk('public')->delete($ourPartner->partner_image);
        }

        $ourPartner->delete();

        return redirect()->route('our-partners.index')->with('success', 'Partner deleted successfully.');
    }
}