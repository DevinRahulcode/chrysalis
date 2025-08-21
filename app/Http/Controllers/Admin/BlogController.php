<?php

namespace App\Http\Controllers\Admin;


use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class BlogController extends Controller
{
    /**
     * Display a listing of the blogs.
     */
    public function index()
    {
        return view('admin.blogs.list');
    }

    /**
     * Show the form for creating a new blog.
     */
    public function create()
    {
        $blogs = Blog::select('id', 'title')->where('status', 'Y')->orderBy('order', 'asc')->get();
        return view('admin.blogs.create', compact('blogs'));
    }

    /**
     * Store a newly created blog in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
            'card_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'description' => 'nullable|string',
            'other_blogs_description' => 'nullable|string',
            'listing_description' => 'nullable|string',
            'related_post_id' => 'nullable|array',
            'related_post_id.*' => 'exists:blogs,id',
            'status' => 'required|in:Y,N',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            \DB::beginTransaction();

            $data = [
                'title' => $request->title,
                'order' => $request->order,
                'description' => $request->description,
                'other_blogs_description' => $request->other_blogs_description,
                'listing_description' => $request->listing_description,
                'slug' => Str::slug($request->title, '-'),
                'related_post_id' => $request->related_post_id ? json_encode($request->related_post_id) : null,
                'status' => $request->status,
                'created_by' => Auth::id(),
            ];

            // Handle image upload
            if ($request->hasFile('card_image')) {
                $imagePath = $request->file('card_image')->store('blogs', 'public');
                $data['card_image'] = $imagePath;
            }

            Blog::create($data);

            \DB::commit();

            return redirect()->route('blog.index')->with('success', 'Blog created successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->route('blog.index')->with('error', 'Failed to create blog: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified blog.
     */
    public function show($id)
    {
        $blog = Blog::findOrFail($id);
        $blogs = Blog::where('status', 'Y')->where('id', '!=', $id)->orderBy('order', 'asc')->get();
        $cardImageUrl = $blog->card_image ? Storage::disk('public')->url($blog->card_image) : null;
        $blogRelatedIds = $blog->related_post_id ? json_decode($blog->related_post_id, true) : [];

        return view('admin.blogs.edit', compact('blog', 'blogs', 'cardImageUrl', 'blogRelatedIds'));
    }

    /**
     * Update the specified blog in storage.
     */
    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
            'card_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'description' => 'nullable|string',
            'other_blogs_description' => 'nullable|string',
            'listing_description' => 'nullable|string',
            'related_post_id' => 'nullable|array',
            'related_post_id.*' => 'exists:blogs,id',
            'status' => 'required|in:Y,N',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            \DB::beginTransaction();

            $data = [
                'title' => $request->title,
                'order' => $request->order,
                'description' => $request->description,
                'other_blogs_description' => $request->other_blogs_description,
                'listing_description' => $request->listing_description,
                'slug' => Str::slug($request->title, '-'),
                'related_post_id' => $request->related_post_id ? json_encode($request->related_post_id) : null,
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ];

            // Handle image upload
            if ($request->hasFile('card_image')) {
                // Delete old image if it exists
                if ($blog->card_image) {
                    Storage::disk('public')->delete($blog->card_image);
                }
                $imagePath = $request->file('card_image')->store('blogs', 'public');
                $data['card_image'] = $imagePath;
            }

            $blog->update($data);

            \DB::commit();

            return redirect()->route('blog.index')->with('success', 'Blog updated successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->route('blog.index')->with('error', 'Failed to update blog: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified blog from storage (soft delete).
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();

            $blog = Blog::findOrFail($id);
            $blog->update(['deleted_by' => Auth::id()]);
            $blog->delete();

            \DB::commit();

            return redirect()->route('blog.index')->with('success', 'Blog deleted successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->route('blog.index')->with('error', 'Failed to delete blog: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the activation status of a blog.
     */
    public function activation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:blogs,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('blog.index')->with('error', 'Invalid blog ID.');
        }

        try {
            $blog = Blog::findOrFail($request->id);
            $blog->status = $blog->status === 'Y' ? 'N' : 'Y';
            $blog->save();

            $message = $blog->status === 'Y' ? 'Blog activated successfully.' : 'Blog deactivated successfully.';
            return redirect()->route('blog.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('blog.index')->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve blog data for DataTables.
     */
    public function getAjaxBlogData()
    {
        $model = Blog::query()->orderBy('id', 'desc');

        return DataTables::eloquent($model)
            ->addIndexColumn()
            ->editColumn('title', function ($blog) {
                return $blog->title;
            })
            ->addColumn('edit', function ($blog) {
                $edit_url = route('blog.show', $blog->id);
                return '<a href="' . $edit_url . '"><i class="fal fa-edit"></i></a>';
            })
            ->addColumn('activation', function ($blog) {
                return view('admin.blogs.partials._status', compact('blog'));
            })
            ->addColumn('delete', function ($blog) {
                return view('admin.blogs.partials._delete', compact('blog'));
            })
            ->rawColumns(['edit', 'activation', 'delete'])
            ->toJson();
    }
}
