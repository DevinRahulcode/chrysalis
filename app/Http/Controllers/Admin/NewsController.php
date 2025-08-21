<?php

namespace App\Http\Controllers\Admin;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class NewsController extends Controller
{
    /**
     * Display a listing of the news.
     */
    public function index()
    {
        return view('admin.news.list');
    }

    /**
     * Show the form for creating a new news item.
     */
    public function create()
    {
        $news = News::select('id', 'news_title')->where('status', 'Y')->orderBy('order', 'asc')->get();
        return view('admin.news.create', compact('news'));
    }

    /**
     * Store a newly created news item in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'news_title' => 'required|string|max:255',
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
                'news_title' => $request->news_title,
                'order' => $request->order,
                'date' => $request->date,
                'news_description' => $request->news_description,
                'slug' => Str::slug($request->news_title, '-'),
                'status到以下代码结束为止：' => $request->status,
                'page_title' => $request->page_title,
                'description' => $request->description,
                'keywords' => $request->keywords,
                'og_title' => $request->og_title,
                'og_description' => $request->og_description,
                'created_by' => Auth::id(),
            ];

            // Handle thumbnail upload
            if ($request->hasFile('thumbnail')) {
                $imagePath = $request->file('thumbnail')->store('news', 'public');
                $data['thumbnail'] = $imagePath;
            }

            // Handle og_image upload
            if ($request->hasFile('og_image')) {
                $ogImagePath = $request->file('og_image')->store('news', 'public');
                $data['og_image'] = $ogImagePath;
            }

            News::create($data);

            \DB::commit();

            return redirect()->route('news.index')->with('success', 'News item created successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->route('news.index')->with('error', 'Failed to create news item: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified news item.
     */
    public function show($id)
    {
        $news = News::findOrFail($id);
        $newsItems = News::where('status', 'Y')->where('id', '!=', $id)->orderBy('order', 'asc')->get();
        $thumbnailUrl = $news->thumbnail ? Storage::disk('public')->url($news->thumbnail) : null;
        $ogImageUrl = $news->og_image ? Storage::disk('public')->url($news->og_image) : null;

        return view('admin.news.edit', compact('news', 'newsItems', 'thumbnailUrl', 'ogImageUrl'));
    }

    /**
     * Update the specified news item in storage.
     */
    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'news_title' => 'required|string|max:255',
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
                'news_title' => $request->news_title,
                'order' => $request->order,
                'date' => $request->date,
                'news_description' => $request->news_description,
                'slug' => Str::slug($request->news_title, '-'),
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
                if ($news->thumbnail) {
                    Storage::disk('public')->delete($news->thumbnail);
                }
                $imagePath = $request->file('thumbnail')->store('news', 'public');
                $data['thumbnail'] = $imagePath;
            }

            // Handle og_image upload
            if ($request->hasFile('og_image')) {
                // Delete old og_image if it exists
                if ($news->og_image) {
                    Storage::disk('public')->delete($news->og_image);
                }
                $ogImagePath = $request->file('og_image')->store('news', 'public');
                $data['og_image'] = $ogImagePath;
            }

            $news->update($data);

            \DB::commit();

            return redirect()->route('news.index')->with('success', 'News item updated successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->route('news.index')->with('error', 'Failed to update news item: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified news item from storage (soft delete).
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();

            $news = News::findOrFail($id);
            $news->update(['deleted_by' => Auth::id()]);
            $news->delete();

            \DB::commit();

            return redirect()->route('news.index')->with('success', 'News item deleted successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->route('news.index')->with('error', 'Failed to delete news item: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the activation status of a news item.
     */
    public function activation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:news,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('news.index')->with('error', 'Invalid news ID.');
        }

        try {
            $news = News::findOrFail($request->id);
            $news->status = $news->status === 'Y' ? 'N' : 'Y';
            $news->save();

            $message = $news->status === 'Y' ? 'News item activated successfully.' : 'News item deactivated successfully.';
            return redirect()->route('news.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('news.index')->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve news data for DataTables.
     */
    public function getAjaxNewsData()
    {
        $model = News::query()->orderBy('id', 'desc');

        return DataTables::eloquent($model)
            ->addIndexColumn()
            ->editColumn('news_title', function ($news) {
                return $news->news_title;
            })
            ->addColumn('edit', function ($news) {
                $edit_url = route('news.show', $news->id);
                return '<a href="' . $edit_url . '"><i class="fal fa-edit"></i></a>';
            })
            ->addColumn('activation', function ($news) {
                return view('admin.news.partials._status', compact('news'));
            })
            ->addColumn('delete', function ($news) {
                return view('admin.news.partials._delete', compact('news'));
            })
            ->rawColumns(['edit', 'activation', 'delete'])
            ->toJson();
    }
}