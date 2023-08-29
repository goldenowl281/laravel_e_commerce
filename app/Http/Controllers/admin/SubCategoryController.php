<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\SubCategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use PhpParser\Node\Expr\FuncCall;

class SubCategoryController extends Controller
{
    //TO SHOW DATA LISTING
    public function index(Request $request)
    {
        $sub_categories = SubCategory::select('sub_categories.*', 'categories.name as categoryName')
            ->latest('sub_categories.id')
            ->leftJoin('categories', 'categories.id', '=', 'sub_categories.category_id');

        if (!empty($request->get('table_search'))) {
            $sub_categories = $sub_categories->where('sub_categories.name', 'like', '%' . $request->get('table_search') . '%');

            $sub_categories = $sub_categories->orWhere('categories.name', 'like', '%' . $request->get('table_search') . '%');
        }

        $sub_categories = $sub_categories->paginate(5);
        return view('admin.sub-category.list', compact('sub_categories'));
    }

    // TO  CREATE CATEGORY
    public function create()
    {
        $categories = Category::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        return view('admin.sub-category.create', $data);
    }
    // TO STORE DATA IN DATABASE
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'slug'     => 'required|unique:sub_categories',
            'category' => 'required',
            'status'   => 'required'
        ]);
        if ($validator->passes()) {
            $sub_category = new SubCategory();
            $sub_category->name         = $request->name;
            $sub_category->slug         = $request->slug;
            $sub_category->status       = $request->status;
            $sub_category->category_id  = $request->category;
            $sub_category->save();

            // $request->session()->flash('success', 'Sub Category created successfully');
            Session::flash('success', 'Sub Category created successfully');
            return response([
                'status' => true,
                'message' => 'Sub Category created successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }
    // TO EDIT
    public function edit($id, Request $request)
    {
        $sub_categories = SubCategory::find($id);
        if (empty($sub_categories)) {
            Session::flash('error', 'Sub Categories DAta not found');
            return redirect()->route('sub-categories.index');
        }

        $categories = Category::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        $data['sub_categories'] = $sub_categories;
        return view('admin.sub-category.edit', $data);
    }
    //To STORE DATA AFTER EDIT
    public function update($id, Request $request)
    {
        $sub_category = SubCategory::find($id);
        if (empty($sub_category)) {

            Session::flash('error', 'Category not found');
            return response()->json([
                'status'   => false,
                'notFound' => true,
                'message'  => 'Category not found'
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'slug'     => "required|unique:sub_categories,slug,$sub_category->id,id",
            'category' => 'required',
            'status'   => 'required'
        ]);
        if ($validator->passes()) {
            $sub_category->name         = $request->name;
            $sub_category->slug         = $request->slug;
            $sub_category->status       = $request->status;
            $sub_category->category_id  = $request->category;
            $sub_category->save();

            Session::flash('success', 'Sub Category updated successfully');
            return response([
                'status' => true,
                'message' => 'Sub Category updated successfully'
            ]);
        }
    }
            // TO DELETE DATA
    public function destory($id, Request $request)
    {
        $sub_category = SubCategory::find($id);

        if (empty($sub_category)) {
            Session::flash('error', 'Category not found');
            return response()->json([
                'status' => true,
                'message' => 'Category not found',
                'error'  => 'Category not found'
            ]);
        }
        $sub_category->delete();
        Session::flash('success', 'Category deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'category deleted success'
        ]);
    }
}
