<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subsidiaries;
use App\Models\SubsidiariesCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SubsidiariesCardController extends Controller
{
    /**
     * Display a listing of the subsidiary cards.
     */
    public function index()
    {
        $subsidiaryCards = SubsidiariesCard::with('subsidiary')->get();
        return view('subsidiary_cards.index', compact('subsidiaryCards'));
    }

    /**
     * Show the form for creating a new subsidiary card.
     */
    public function create()
    {
        $subsidiaries = Subsidiaries::where('status', 'Y')->get();
        return view('subsidiary_cards.create', compact('subsidiaries'));
    }

    /**
     * Store a newly created subsidiary card in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subsidiaries_id' => 'required|exists:subsidiaries,id',
            'card_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'card_heading' => 'required|string|max:255',
            'card_description' => 'nullable|string',
            'card_url' => 'nullable|url|max:255',
            'status' => 'required|in:Y,N',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'subsidiaries_id' => $request->subsidiaries_id,
            'card_heading' => $request->card_heading,
            'card_description' => $request->card_description,
            'card_url' => $request->card_url,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ];

        // Handle image upload
        if ($request->hasFile('card_image')) {
            $imagePath = $request->file('card_image')->store('subsidiary_cards', 'public');
            $data['card_image'] = $imagePath;
        }

        SubsidiariesCard::create($data);

        return redirect()->route('subsidiary_cards.index')->with('success', 'Subsidiary card created successfully.');
    }

    /**
     * Display the specified subsidiary card.
     */
    public function show(SubsidiariesCard $subsidiaryCard)
    {
        $subsidiaryCard->load('subsidiary');
        return view('subsidiary_cards.show', compact('subsidiaryCard'));
    }

    /**
     * Show the form for editing the specified subsidiary card.
     */
    public function edit(SubsidiariesCard $subsidiaryCard)
    {
        $subsidiaries = Subsidiaries::where('status', 'Y')->get();
        return view('subsidiary_cards.edit', compact('subsidiaryCard', 'subsidiaries'));
    }

    /**
     * Update the specified subsidiary card in storage.
     */
    public function update(Request $request, SubsidiariesCard $subsidiaryCard)
    {
        $validator = Validator::make($request->all(), [
            'subsidiaries_id' => 'required|exists:subsidiaries,id',
            'card_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'card_heading' => 'required|string|max:255',
            'card_description' => 'nullable|string',
            'card_url' => 'nullable|url|max:255',
            'status' => 'required|in:Y,N',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'subsidiaries_id' => $request->subsidiaries_id,
            'card_heading' => $request->card_heading,
            'card_description' => $request->card_description,
            'card_url' => $request->card_url,
            'status' => $request->status,
            'updated_by' => Auth::id(),
        ];

        // Handle image upload
        if ($request->hasFile('card_image')) {
            // Delete old image if it exists
            if ($subsidiaryCard->card_image) {
                Storage::disk('public')->delete($subsidiaryCard->card_image);
            }
            $imagePath = $request->file('card_image')->store('subsidiary_cards', 'public');
            $data['card_image'] = $imagePath;
        }

        $subsidiaryCard->update($data);

        return redirect()->route('subsidiary_cards.index')->with('success', 'Subsidiary card updated successfully.');
    }

    /**
     * Remove the specified subsidiary card from storage (soft delete).
     */
    public function destroy(SubsidiariesCard $subsidiaryCard)
    {
        $subsidiaryCard->update([
            'deleted_by' => Auth::id(),
        ]);
        $subsidiaryCard->delete();

        return redirect()->route('subsidiary_cards.index')->with('success', 'Subsidiary card deleted successfully.');
    }
}
