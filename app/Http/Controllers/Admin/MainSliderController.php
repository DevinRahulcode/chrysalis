<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomeMainSlider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MainSliderController extends Controller
{
    public function index()
    {
        $sliders = HomeMainSlider::with('homePage')->get();
        return view('home_main_sliders.index', compact('sliders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('home_main_sliders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'home_id' => 'nullable|exists:home_page,id',
            'slider_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'slider_heading' => 'nullable|string|max:255',
            'slider_description' => 'nullable|string',
            'status' => 'required|in:Y,N',
        ]);

        $data = $validated;
        $data['created_by'] = Auth::id();

        if ($request->hasFile('slider_image')) {
            $path = $request->file('slider_image')->store('slider_images', 'public');
            $data['slider_image'] = $path;
        }

        HomeMainSlider::create($data);

        return redirect()->route('home-main-sliders.index')->with('success', 'Slider created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(HomeMainSlider $homeMainSlider)
    {
        return view('home_main_sliders.show', compact('homeMainSlider'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HomeMainSlider $homeMainSlider)
    {
        return view('home_main_sliders.edit', compact('homeMainSlider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HomeMainSlider $homeMainSlider)
    {
        $validated = $request->validate([
            'home_id' => 'nullable|exists:home_page,id',
            'slider_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'slider_heading' => 'nullable|string|max:255',
            'slider_description' => 'nullable|string',
            'status' => 'required|in:Y,N',
        ]);

        $data = $validated;
        $data['updated_by'] = Auth::id();

        if ($request->hasFile('slider_image')) {
            // Delete old image if it exists
            if ($homeMainSlider->slider_image) {
                Storage::disk('public')->delete($homeMainSlider->slider_image);
            }
            $path = $request->file('slider_image')->store('slider_images', 'public');
            $data['slider_image'] = $path;
        }

        $homeMainSlider->update($data);

        return redirect()->route('home-main-sliders.index')->with('success', 'Slider updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HomeMainSlider $homeMainSlider)
    {
        $homeMainSlider->deleted_by = Auth::id();
        $homeMainSlider->save();
        
        // Delete the associated image
        if ($homeMainSlider->slider_image) {
            Storage::disk('public')->delete($homeMainSlider->slider_image);
        }

        $homeMainSlider->delete();

        return redirect()->route('home-main-sliders.index')->with('success', 'Slider deleted successfully.');
    }
}


