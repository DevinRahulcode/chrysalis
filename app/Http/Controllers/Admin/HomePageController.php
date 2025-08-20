<?php

namespace App\Http\Controllers;

use App\Models\HomePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class HomePageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $homePages = HomePage::withTrashed()->get();
        return view('home_pages.index', compact('homePages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('home_pages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'linkedin_link' => 'nullable|url|max:255',
            'youtube_link' => 'nullable|url|max:255',
            'facebook_link' => 'nullable|url|max:255',
            'instagram_link' => 'nullable|url|max:255',
            'x_link' => 'nullable|url|max:255',
            'about_section_heading' => 'nullable|string|max:255',
            'about_section_description' => 'nullable|string',
            'our_business_heading' => 'nullable|string|max:255',
            'our_business_description' => 'nullable|string',
            'icon_one' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text_one' => 'nullable|string|max:255',
            'icon_two' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text_two' => 'nullable|string|max:255',
            'icon_three' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text_three' => 'nullable|string|max:255',
            'icon_four' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text_four' => 'nullable|string|max:255',
            'icon_five' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text_five' => 'nullable|string|max:255',
            'icon_six' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text_six' => 'nullable|string|max:255',
            'icon_seven' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text_seven' => 'nullable|string|max:255',
            'image_icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'your_contribution_heading' => 'nullable|string|max:255',
            'your_contribution_description' => 'nullable|string',
            'your_contribution_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $validated;
        $data['created_by'] = Auth::id();

        // Handle image uploads
        $imageFields = [
            'icon_one', 'icon_two', 'icon_three', 'icon_four',
            'icon_five', 'icon_six', 'icon_seven', 'image_icon',
            'your_contribution_image'
        ];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('home_page_images', 'public');
            }
        }

        HomePage::create($data);

        return redirect()->route('home_pages.index')->with('success', 'Home page created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(HomePage $homePage)
    {
        return view('home_pages.show', compact('homePage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HomePage $homePage)
    {
        return view('home_pages.edit', compact('homePage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HomePage $homePage)
    {
        $validated = $request->validate([
            'linkedin_link' => 'nullable|url|max:255',
            'youtube_link' => 'nullable|url|max:255',
            'facebook_link' => 'nullable|url|max:255',
            'instagram_link' => 'nullable|url|max:255',
            'x_link' => 'nullable|url|max:255',
            'about_section_heading' => 'nullable|string|max:255',
            'about_section_description' => 'nullable|string',
            'our_business_heading' => 'nullable|string|max:255',
            'our_business_description' => 'nullable|string',
            'icon_one' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text_one' => 'nullable|string|max:255',
            'icon_two' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text_two' => 'nullable|string|max:255',
            'icon_three' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text_three' => 'nullable|string|max:255',
            'icon_four' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text_four' => 'nullable|string|max:255',
            'icon_five' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text_five' => 'nullable|string|max:255',
            'icon_six' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text_six' => 'nullable|string|max:255',
            'icon_seven' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text_seven' => 'nullable|string|max:255',
            'image_icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'your_contribution_heading' => 'nullable|string|max:255',
            'your_contribution_description' => 'nullable|string',
            'your_contribution_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $validated;
        $data['updated_by'] = Auth::id();

        // Handle image uploads and delete old images if new ones are uploaded
        $imageFields = [
            'icon_one', 'icon_two', 'icon_three', 'icon_four',
            'icon_five', 'icon_six', 'icon_seven', 'image_icon',
            'your_contribution_image'
        ];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old image if exists
                if ($homePage->$field) {
                    Storage::disk('public')->delete($homePage->$field);
                }
                $data[$field] = $request->file($field)->store('home_page_images', 'public');
            }
        }

        $homePage->update($data);

        return redirect()->route('home_pages.index')->with('success', 'Home page updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HomePage $homePage)
    {
        $homePage->deleted_by = Auth::id();
        $homePage->save();
        $homePage->delete();

        return redirect()->route('home_pages.index')->with('success', 'Home page deleted successfully.');
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore($id)
    {
        $homePage = HomePage::onlyTrashed()->findOrFail($id);
        $homePage->restore();

        return redirect()->route('home_pages.index')->with('success', 'Home page restored successfully.');
    }
}