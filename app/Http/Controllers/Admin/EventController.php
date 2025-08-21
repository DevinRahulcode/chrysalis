<?php

namespace App\Http\Controllers\Admin;


use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class EventController extends Controller
{
    /**
     * Display a listing of the events.
     */
    public function index()
    {
        return view('admin.events.list');
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        $events = Event::select('id', 'event_title')->where('status', 'Y')->orderBy('order', 'asc')->get();
        return view('admin.events.create', compact('events'));
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_title' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
            'event_hero_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'event_card_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'event_description' => 'nullable|string',
            'other_event_description' => 'nullable|string',
            'event_listing_description' => 'nullable|string',
            'event_related_post_id' => 'nullable|array',
            'event_related_post_id.*' => 'exists:events,id',
            'status' => 'required|in:Y,N',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            \DB::beginTransaction();

            $data = [
                'event_title' => $request->event_title,
                'order' => $request->order,
                'event_description' => $request->event_description,
                'other_event_description' => $request->other_event_description,
                'event_listing_description' => $request->event_listing_description,
                'slug' => Str::slug($request->event_title, '-'),
                'event_related_post_id' => $request->event_related_post_id ? json_encode($request->event_related_post_id) : null,
                'status' => $request->status,
                'created_by' => Auth::id(),
            ];

            // Handle hero image upload
            if ($request->hasFile('event_hero_image')) {
                $imagePath = $request->file('event_hero_image')->store('events', 'public');
                $data['event_hero_image'] = $imagePath;
            }

            // Handle card image upload
            if ($request->hasFile('event_card_image')) {
                $imagePath = $request->file('event_card_image')->store('events', 'public');
                $data['event_card_image'] = $imagePath;
            }

            Event::create($data);

            \DB::commit();

            return redirect()->route('events.index')->with('success', 'Event created successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->route('events.index')->with('error', 'Failed to create event: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified event.
     */
    public function show($id)
    {
        $event = Event::findOrFail($id);
        $events = Event::where('status', 'Y')->where('id', '!=', $id)->orderBy('order', 'asc')->get();
        $heroImageUrl = $event->event_hero_image ? Storage::disk('public')->url($event->event_hero_image) : null;
        $cardImageUrl = $event->event_card_image ? Storage::disk('public')->url($event->event_card_image) : null;
        $eventRelatedIds = $event->event_related_post_id ? json_decode($event->event_related_post_id, true) : [];

        return view('admin.events.edit', compact('event', 'events', 'heroImageUrl', 'cardImageUrl', 'eventRelatedIds'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'event_title' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
            'event_hero_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'event_card_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'event_description' => 'nullable|string',
            'other_event_description' => 'nullable|string',
            'event_listing_description' => 'nullable|string',
            'event_related_post_id' => 'nullable|array',
            'event_related_post_id.*' => 'exists:events,id',
            'status' => 'required|in:Y,N',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            \DB::beginTransaction();

            $data = [
                'event_title' => $request->event_title,
                'order' => $request->order,
                'event_description' => $request->event_description,
                'other_event_description' => $request->other_event_description,
                'event_listing_description' => $request->event_listing_description,
                'slug' => Str::slug($request->event_title, '-'),
                'event_related_post_id' => $request->event_related_post_id ? json_encode($request->event_related_post_id) : null,
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ];

            // Handle hero image upload
            if ($request->hasFile('event_hero_image')) {
                // Delete old hero image if it exists
                if ($event->event_hero_image) {
                    Storage::disk('public')->delete($event->event_hero_image);
                }
                $imagePath = $request->file('event_hero_image')->store('events', 'public');
                $data['event_hero_image'] = $imagePath;
            }

            // Handle card image upload
            if ($request->hasFile('event_card_image')) {
                // Delete old card image if it exists
                if ($event->event_card_image) {
                    Storage::disk('public')->delete($event->event_card_image);
                }
                $imagePath = $request->file('event_card_image')->store('events', 'public');
                $data['event_card_image'] = $imagePath;
            }

            $event->update($data);

            \DB::commit();

            return redirect()->route('events.index')->with('success', 'Event updated successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->route('events.index')->with('error', 'Failed to update event: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified event from storage (soft delete).
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();

            $event = Event::findOrFail($id);
            $event->update(['deleted_by' => Auth::id()]);
            $event->delete();

            \DB::commit();

            return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->route('events.index')->with('error', 'Failed to delete event: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the activation status of an event.
     */
    public function activation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:events,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('events.index')->with('error', 'Invalid event ID.');
        }

        try {
            $event = Event::findOrFail($request->id);
            $event->status = $event->status === 'Y' ? 'N' : 'Y';
            $event->save();

            $message = $event->status === 'Y' ? 'Event activated successfully.' : 'Event deactivated successfully.';
            return redirect()->route('events.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('events.index')->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve event data for DataTables.
     */
    public function getAjaxEventData()
    {
        $model = Event::query()->orderBy('id', 'desc');

        return DataTables::eloquent($model)
            ->addIndexColumn()
            ->editColumn('event_title', function ($event) {
                return $event->event_title;
            })
            ->addColumn('edit', function ($event) {
                $edit_url = route('events.show', $event->id);
                return '<a href="' . $edit_url . '"><i class="fal fa-edit"></i></a>';
            })
            ->addColumn('activation', function ($event) {
                return view('admin.events.partials._status', compact('event'));
            })
            ->addColumn('delete', function ($event) {
                return view('admin.events.partials._delete', compact('event'));
            })
            ->rawColumns(['edit', 'activation', 'delete'])
            ->toJson();
    }
}
