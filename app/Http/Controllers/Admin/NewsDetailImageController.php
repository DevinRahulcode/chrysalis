<?php

namespace App\Http\Controllers\Admin;

use App\Models\Blog;
use App\Models\BlogDetailImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class BlogDetailImageController extends Controller
{
    /**
     * Display a listing of the blog detail images.
     */
    public function index()
    {
        return view('admin.blog_detail_images.list');
    }

    /**
     * Show the form for creating a new blog detail image.
     */
    public function create()
    {
        $blogs = Blog::select('id', 'title')->where('status', 'Y')->orderBy('title', 'asc')->get();
        return view('admin.blog_detail_images.create', compact('blogs'));
    }

    /**
     * Store a newly created blog detail image in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'blog_id' => 'required|exists:blogs,id',
            'blog_image_slider' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            \DB::beginTransaction();

            $data = [
                'blog_id' => $request->blog_id,
            ];

            // Handle image upload
            if ($request->hasFile('blog_image_slider')) {
                $imagePath = $request->file('blog_image_slider')->store('blog_detail_images', 'public');
                $data['blog_image_slider'] = $imagePath;
            }

            BlogDetailImage::create($data);

            \DB::commit();

            return redirect()->route('blog_detail_images.index')->with('success', 'Blog detail image created successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->route('blog_detail_images.index')->with('error', 'Failed to create blog detail image: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified blog detail image.
     */
    public function show($id)
    {
        $blogDetailImage = BlogDetailImage::findOrFail($id);
        $blogs = Blog::select('id', 'title')->where('status', 'Y')->orderBy('title', 'asc')->get();
        $imageSliderUrl = $blogDetailImage->blog_image_slider ? Storage::disk('public')->url($blogDetailImage->blog_image_slider) : null;

        return view('admin.blog_detail_images.edit', compact('blogDetailImage', 'blogs', 'imageSliderUrl'));
    }

    /**
     * Update the specified blog detail image in storage.
     */
    public function update(Request $request, $id)
    {
        $blogDetailImage = BlogDetailImage::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'blog_id' => 'required|exists:blogs,id',
            'blog_image_slider' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            \DB::beginTransaction();

            $data = [
                'blog_id' => $request->blog_id,
            ];

            // Handle image upload
            if ($request->hasFile('blog_image_slider')) {
                // Delete old image if it exists
                if ($blogDetailImage->blog_image_slider) {
                    Storage::disk('public')->delete($blogDetailImage->blog_image_slider);
                }
                $imagePath = $request->file('blog_image_slider')->store('blog_detail_images', 'public');
                $data['blog_image_slider'] = $imagePath;
            }

            $blogDetailImage->update($data);

            \DB::commit();

            return redirect()->route('blog_detail_images.index')->with('success', 'Blog detail image updated successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->route('blog_detail_images.index')->with('error', 'Failed to update blog detail image: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified blog detail image from storage (soft delete).
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();

            $blogDetailImage = BlogDetailImage::findOrFail($id);
            $blogDetailImage->delete();

            \DB::commit();

            return redirect()->route('blog_detail_images.index')->with('success', 'Blog detail image deleted successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->route('blog_detail_images.index')->with('error', 'Failed to delete blog detail image: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve blog detail image data for DataTables.
     */
    public function getAjaxBlogDetailImageData()
    {
        $model = BlogDetailImage::query()->with('blog')->orderBy('id', 'desc');

        return DataTables::eloquent($model)
            ->addIndexColumn()
            ->editColumn('blog_id', function ($blogDetailImage) {
                return $blogDetailImage->blog ? $blogDetailImage->blog->title : 'N/A';
            })
            ->addColumn('blog_image_slider', function ($blogDetailImage) {
                return $blogDetailImage->blog_image_slider ? '<img src="' . Storage::disk('public')->url($blogDetailImage->blog_image_slider) . '" width="50" alt="Slider Image">' : 'No Image';
            })
            ->addColumn('edit', function ($blogDetailImage) {
                $edit_url = route('blog_detail_images.show', $blogDetailImage->id);
                return '<a href="' . $edit_url . '"><i class="fal fa-edit"></i></a>';
            })
            ->addColumn('delete', function ($blogDetailImage) {
                return view('admin.blog_detail_images.partials._delete', compact('blogDetailImage'));
            })
            ->rawColumns(['blog_image_slider', 'edit', 'delete'])
            ->toJson();
    }
}