<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReviewsHomePage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ReviewHomeController extends Controller
{
      public function index()
    {
        $reviews = ReviewsHomePage::withTrashed()->get();
        return view('reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('reviews.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'home_id' => 'required|exists:home_page,id',
            'reviews_heading' => 'nullable|string|max:255',
            'reviews_heading_description' => 'nullable|string',
            'testimonial' => 'nullable|string',
            'reviewer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'reviewer_name' => 'nullable|string|max:100',
            'reviewer_designation' => 'nullable|string|max:100',
            'status' => 'required|in:Y,N',
        ]);

        $data = $validated;
        $data['created_by'] = Auth::id();

        if ($request->hasFile('reviewer_image')) {
            $data['reviewer_image'] = $request->file('reviewer_image')->store('reviewer_images', 'public');
        }

        ReviewsHomePage::create($data);

        return redirect()->route('reviews.index')->with('success', 'Review created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ReviewsHomePage $review)
    {
        return view('reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReviewsHomePage $review)
    {
        return view('reviews.edit', compact('review'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReviewsHomePage $review)
    {
        $validated = $request->validate([
            'home_id' => 'required|exists:home_page,id',
            'reviews_heading' => 'nullable|string|max:255',
            'reviews_heading_description' => 'nullable|string',
            'testimonial' => 'nullable|string',
            'reviewer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'reviewer_name' => 'nullable|string|max:100',
            'reviewer_designation' => 'nullable|string|max:100',
            'status' => 'required|in:Y,N',
        ]);

        $data = $validated;
        $data['updated_by'] = Auth::id();

        if ($request->hasFile('reviewer_image')) {
            // Delete old image if exists
            if ($review->reviewer_image) {
                Storage::disk('public')->delete($review->reviewer_image);
            }
            $data['reviewer_image'] = $request->file('reviewer_image')->store('reviewer_images', 'public');
        }

        $review->update($data);

        return redirect()->route('reviews.index')->with('success', 'Review updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReviewsHomePage $review)
    {
        $review->deleted_by = Auth::id();
        $review->save();
        $review->delete();

        return redirect()->route('reviews.index')->with('success', 'Review deleted successfully.');
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore($id)
    {
        $review = ReviewsHomePage::onlyTrashed()->findOrFail($id);
        $review->restore();

        return redirect()->route('reviews.index')->with('success', 'Review restored successfully.');
    }
}
