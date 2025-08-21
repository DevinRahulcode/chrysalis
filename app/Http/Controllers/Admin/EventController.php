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
            'date' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'news_description' => 'nullable|string',
            'status' => 'required|in:Y,N',
            'page_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'keywords' => 'nullable|string',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            \DB::beginTransaction();

            $data = [
                'event_title' => $request->event_title,
                'order' => $request->order,
                'date' => $request->date,
                'news_description' => $request->news_description,
                'slug' => Str::slug($request->event_title, '-'),
                'status' => $request->status,
                'page_title' => $request->page_title,
                'description' => $request->description,
                'keywords' => $request->keywords,
                'og_title' => $request->og_title,
                'og_description' => $request->og_description,
                'created_by' => Auth::id(),
            ];

            // Handle thumbnail upload
            if ($request->hasFile('thumbnail')) {
                $imagePath = $request->file('thumbnail')->store('events', 'public');
                $data['thumbnail'] = $imagePath;
            }

            // Handle og_image upload
            if ($request->hasFile('og_image')) {
                $ogImagePath = $request->file('og_image')->store('events', 'public');
                $data['og_image'] = $ogImagePath;
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
        $thumbnailUrl = $event->thumbnail ? Storage::disk('public')->url($event->thumbnail) : null;
        $ogImageUrl = $event->og_image ? Storage::disk('public')->url($event->og_image) : null;

        return view('admin.events.edit', compact('event', 'events', 'thumbnailUrl', 'ogImageUrl'));
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
            'date' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'news_description' => 'nullable|string',
            'status' => 'required|in:Y,N',
            'page_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'keywords' => 'nullable|string',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            \DB::beginTransaction();

            $data = [
                'event_title' => $request->event_title,
                'order' => $request->order,
                'date' => $request->date,
                'news_description' => $request->news_description,
                'slug' => Str::slug($request->event_title, '-'),
                'status' => $request->status,
                'page_title' => $request->page_title,
                'description' => $request->description,
                'keywords' => $request->keywords,
                'og_title' => $request->og_title,
                'og_description' => $request->og_description,
                'updated_by' => Auth::id(),
            ];

            // Handle thumbnail upload
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail if it exists
                if ($event->thumbnail) {
                    Storage::disk('public')->delete($event->thumbnail);
                }
                $imagePath = $request->file('thumbnail')->store('events', 'public');
                $data['thumbnail'] = $imagePath;
            }

            // Handle og_image upload
            if ($request->hasFile('og_image')) {
                // Delete old og_image if it exists
                if ($event->og_image) {
                    Storage::disk('public')->delete($event->og_image);
                }
                $ogImagePath = $request->file('og_image')->store('events', 'public');
                $data['og_image'] = $ogImagePath;
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