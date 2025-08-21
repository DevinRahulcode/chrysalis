<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use App\Models\EventDetailImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class EventDetailImageController extends Controller
{
    /**
     * Display a listing of the event detail images.
     */
    public function index()
    {
        return view('admin.event_detail_images.list');
    }

    /**
     * Show the form for creating a new event detail image.
     */
    public function create()
    {
        $events = Event::select('id', 'event_title')->where('status', 'Y')->orderBy('event_title', 'asc')->get();
        return view('admin.event_detail_images.create', compact('events'));
    }

    /**
     * Store a newly created event detail image in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'event_image_slider' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'order' => 'nullable|integer|min:0',
            'status' => 'required|in:Y,N',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            \DB::beginTransaction();

            $data = [
                'event_id' => $request->event_id,
                'order' => $request->order,
                'status' => $request->status,
                'created_by' => Auth::id(),
            ];

            // Handle image upload
            if ($request->hasFile('event_image_slider')) {
                $imagePath = $request->file('event_image_slider')->store('event_detail_images', 'public');
                $data['event_image_slider'] = $imagePath;
            }

            EventDetailImage::create($data);

            \DB::commit();

            return redirect()->route('event_detail_images.index')->with('success', 'Event detail image created successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->route('event_detail_images.index')->with('error', 'Failed to create event detail image: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified event detail image.
     */
    public function show($id)
    {
        $eventDetailImage = EventDetailImage::findOrFail($id);
        $events = Event::select('id', 'event_title')->where('status', 'Y')->orderBy('event_title', 'asc')->get();
        $imageSliderUrl = $eventDetailImage->event_image_slider ? Storage::disk('public')->url($eventDetailImage->event_image_slider) : null;

        return view('admin.event_detail_images.edit', compact('eventDetailImage', 'events', 'imageSliderUrl'));
    }

    /**
     * Update the specified event detail image in storage.
     */
    public function update(Request $request, $id)
    {
        $eventDetailImage = EventDetailImage::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'event_image_slider' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'order' => 'nullable|integer|min:0',
            'status' => 'required|in:Y,N',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            \DB::beginTransaction();

            $data = [
                'event_id' => $request->event_id,
                'order' => $request->order,
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ];

            // Handle image upload
            if ($request->hasFile('event_image_slider')) {
                // Delete old image if it exists
                if ($eventDetailImage->event_image_slider) {
                    Storage::disk('public')->delete($eventDetailImage->event_image_slider);
                }
                $imagePath = $request->file('event_image_slider')->store('event_detail_images', 'public');
                $data['event_image_slider'] = $imagePath;
            }

            $eventDetailImage->update($data);

            \DB::commit();

            return redirect()->route('event_detail_images.index')->with('success', 'Event detail image updated successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->route('event_detail_images.index')->with('error', 'Failed to update event detail image: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified event detail image from storage (soft delete).
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();

            $eventDetailImage = EventDetailImage::findOrFail($id);
            $eventDetailImage->update(['deleted_by' => Auth::id()]);
            $eventDetailImage->delete();

            \DB::commit();

            return redirect()->route('event_detail_images.index')->with('success', 'Event detail image deleted successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->route('event_detail_images.index')->with('error', 'Failed to delete event detail image: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve event detail image data for DataTables.
     */
    public function getAjaxEventDetailImageData()
    {
        $model = EventDetailImage::query()->with('event')->orderBy('id', 'desc');

        return DataTables::eloquent($model)
            ->addIndexColumn()
            ->editColumn('event_id', function ($eventDetailImage) {
                return $eventDetailImage->event ? $eventDetailImage->event->event_title : 'N/A';
            })
            ->editColumn('status', function ($eventDetailImage) {
                return $eventDetailImage->status === 'Y' ? 'Active' : 'Inactive';
            })
            ->addColumn('event_image_slider', function ($eventDetailImage) {
                return $eventDetailImage->event_image_slider ? '<img src="' . Storage::disk('public')->url($eventDetailImage->event_image_slider) . '" width="50" alt="Slider Image">' : 'No Image';
            })
            ->addColumn('edit', function ($eventDetailImage) {
                $edit_url = route('event_detail_images.show', $eventDetailImage->id);
                return '<a href="' . $edit_url . '"><i class="fal fa-edit"></i></a>';
            })
            ->addColumn('delete', function ($eventDetailImage) {
                return view('admin.event_detail_images.partials._delete', compact('eventDetailImage'));
            })
            ->rawColumns(['event_image_slider', 'edit', 'delete'])
            ->toJson();
    }
}