<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;




class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::latest();

        if (!empty($request->get('table_search'))) {
            $categories = $categories->where('name', 'like', '%' . $request->get('table_search') . '%');
        }

        $categories = $categories->paginate(5);
        return view('admin.category.list', compact('categories'));
    }
    public function create()
    {
        return view('admin.category.create');
    }

    //DATA STORE FUNCTION
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories',
        ]);

        if ($validator->passes()) {
            $category = new Category();
            $category->name      = $request->name;
            $category->slug      = $request->slug;
            $category->status    = $request->status;
            $category->show_home = $request->show_home;
            $category->save();

            // save image
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray  = explode('.', $tempImage->name);
                $extension = last($extArray);

                $newImageName = $category->id . '.' . $extension;
                $sPath = public_path() . '/temp-img/' . $tempImage->name;
                //sourcePath = sPath
                $dPath = public_path() . '/upload-img/category/full-img/' . $newImageName; //destination Path
                File::copy($sPath, $dPath);

                // thumb image save
                $thumbImgPath = public_path() . '/upload-img/category/thumb/' . $newImageName;
                $img = Image::make($sPath);
                // $img->resize(200, 150);
                $img->fit(200, 150, function ($constraint) {
                    $constraint->upsize();
                });
                $img->save($thumbImgPath);

                $category->image = $newImageName;
                $category->save();
            }

            // $request->session()->flash('success', 'Category added successfully');
            Session::flash('success', 'Category added successfully');


            return response()->json([
                'status' => true,
                'message' => 'Category added successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }
    public function edit($id, Request $request)
    {
        $category = Category::find($id);
        if (empty($category)) {
            return redirect()->route('categories.index');
        }
        return view('admin.category.edit', compact('category'));
    }

    // UPDATE FUNCTION
    public function update($id, Request $request)
    {
        $category = Category::find($id);
        if (empty($category)) {
            // $request->session()->flash('error','Category not found');
            Session::flash('error', 'Category not found');

            return response()->json([
                'status'   => false,
                'notFound' => true,
                'message'  => 'Category not found'
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => "required|unique:categories,slug,$category->id,id",
        ]);

        if ($validator->passes()) {
            $category->name      = $request->name;
            $category->slug      = $request->slug;
            $category->status    = $request->status;
            $category->show_home = $request->show_home;
            $category->save();

            $oldImage = $category->image;

            // save image
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray  = explode('.', $tempImage->name);
                $extension = last($extArray);

                $newImageName = $category->id . '-' . time() . '.' . $extension;
                $sPath = public_path() . '/temp-img/' . $tempImage->name;
                //sourcePath = sPath
                $dPath = public_path() . '/upload-img/category/full-img/' . $newImageName; //destination Path
                File::copy($sPath, $dPath);

                // thumb image save
                $thumbImgPath = public_path() . '/upload-img/category/thumb/' . $newImageName;
                $img = Image::make($sPath);
                // $img->resize(200, 150);
                $img->fit(200, 150, function ($constraint) {
                    $constraint->upsize();
                });
                $img->save($thumbImgPath);

                $category->image = $newImageName;
                $category->save();

                // Delete Old images
                File::delete(public_path() . '/upload-img/category/full-img/' . $oldImage);
                File::delete(public_path() . '/upload-img/category/thumb/' . $oldImage);
            }


            Session::flash('success', 'Category updated successfully');
            // $request->session()->flash('success', 'Category updated successfully');

            return response()->json([
                'status' => true,
                'message' => 'Category updated successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }
    public function destory($id, Request $request)
    {
        $category = Category::find($id);
        if (empty($category)) {
            Session::flash('error', 'Category not found');
            // $request->session()->flash('error','Category not found');
            return response()->json([
                'status' => true,
                'message' => 'Category not found',
                'error'  => 'Category not found'
            ]);
        }

        File::delete(public_path() . '/upload-img/category/full-img/' . $category->image);
        File::delete(public_path() . '/upload-img/category/thumb/' . $category->image);
        $category->delete();
        // $request->session()->flash('success','category deleted success');
        Session::flash('success', 'Category deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'category deleted succ'
        ]);
    }
}
