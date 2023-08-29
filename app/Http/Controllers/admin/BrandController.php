<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    // TO SHOW BRAND LIST
    public function index(Request $request)
    {
        $brands = Brand::latest('id');

        if (!empty($request->get('table_search'))) {
            $brands = $brands->where('name', 'like', '%' . $request->get('table_search') . '%');
        }
        $brands = $brands->paginate(5);
        return view('admin.brand.list', compact('brands'));
    }
    //TO CREATE NEW BRAND
    public function create()
    {
        return view('admin.brand.create');
    }
    //AFTER CREATE TO STORE DATA
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'slug'     => 'required|unique:brands',
            'status'   => 'required'
        ]);
        if ($validator->passes()) {
            $brand  = new Brand();
            $brand->name     = $request->name;
            $brand->slug     = $request->slug;
            $brand->status   = $request->status;
            $brand->save();

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
    //TO EDIT DATA
    public function edit($id, Request $request)
    {
        $brands = Brand::find($id);
        if (empty($brands)) {
            Session::flash('error', 'Sub Categories DAta not found');
            return redirect()->route('brand.index');
        }

        $data['brands'] = $brands;
        return view('admin.brand.edit', $data);
    }
    //AFTER EDIT TO STORE DATA UPDATE
    public function update($id, Request $request)
    {
        $brands = Brand::find($id);
        if (empty($brands)) {

            Session::flash('error', 'Category not found');
            return response()->json([
                'status'   => false,
                'notFound' => true,
                'message'  => 'Category not found'
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'slug'     => "required|unique:brands,slug,$brands->id,id",
            'status'   => 'required'
        ]);
        if ($validator->passes()) {
            $brands->name         = $request->name;
            $brands->slug         = $request->slug;
            $brands->status       = $request->status;
            $brands->save();

            Session::flash('success', 'Brand updated successfully');
            return response([
                'status' => true,
                'message' => ' Brand updated successfully'
            ]);
        }
    }
    //TO DELETE DATA
    public function destory($id, Request $request)
    {
        $brands = Brand::find($id);

        if (empty($brands)) {
            Session::flash('error', 'Brand data not found');
            return response()->json([
                'status' => true,
                'message' => 'Brand data not found',
                'error'  => 'Brand data not found'
            ]);
        }
        $brands->delete();
        Session::flash('success', 'Brand data  deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'Brand data deleted success'
        ]);
    }
}
