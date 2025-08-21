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
        $blogs = Blog::select('id', 'blog_title')->where('status', 'Y')->orderBy('order', 'asc')->get();
        return view('admin.blogs.create', compact('blogs'));
    }

    /**
     * Store a newly created blog in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'blog_title' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
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
                'blog_title' => $request->blog_title,
                'order' => $request->order,
                'date' => $request->date,
                'news_description' => $request->news_description,
                'slug' => Str::slug($request->blog_title, '-'),
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
                $imagePath = $request->file('thumbnail')->store('blogs', 'public');
                $data['thumbnail'] = $imagePath;
            }

            // Handle og_image upload
            if ($request->hasFile('og_image')) {
                $ogImagePath = $request->file('og_image')->store('blogs', 'public');
                $data['og_image'] = $ogImagePath;
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
        $thumbnailUrl = $blog->thumbnail ? Storage::disk('public')->url($blog->thumbnail) : null;
        $ogImageUrl = $blog->og_image ? Storage::disk('public')->url($blog->og_image) : null;

        return view('admin.blogs.edit', compact('blog', 'blogs', 'thumbnailUrl', 'ogImageUrl'));
    }

    /**
     * Update the specified blog in storage.
     */
    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'blog_title' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
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
                'blog_title' => $request->blog_title,
                'order' => $request->order,
                'date' => $request->date,
                'news_description' => $request->news_description,
                'slug' => Str::slug($request->blog_title, '-'),
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
                if ($blog->thumbnail) {
                    Storage::disk('public')->delete($blog->thumbnail);
                }
                $imagePath = $request->file('thumbnail')->store('blogs', 'public');
                $data['thumbnail'] = $imagePath;
            }

            // Handle og_image upload
            if ($request->hasFile('og_image')) {
                // Delete old og_image if it exists
                if ($blog->og_image) {
                    Storage::disk('public')->delete($blog->og_image);
                }
                $ogImagePath = $request->file('og_image')->store('blogs', 'public');
                $data['og_image'] = $ogImagePath;
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
            ->editColumn('blog_title', function ($blog) {
                return $blog->blog_title;
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