<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\Brand;
use App\Models\admin\SubCategory;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    //TO SHOW PRODUCT VIEW
    public function index(Request $request)
    {
        $products = Product::latest('id')->with('product_images');
        if ($request->get('table_search') != "") {
            $products = $products->where('title', 'like', '%' . $request->table_search . '%');
        }

        $products = $products->paginate(5);
        $data['products'] = $products;
        return view('admin.products.list', $data);
    }
    // TO CREATE NEW PRODUCTS
    public function create()
    {
        $data = [];
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands     = Brand::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        $data['brands']     = $brands;

        return view('admin.products.create', $data);
    }
    //AFTER CREATE PRODUCT TO STORE DATA
    public function store(Request $request)
    {
        $rules = [
            'title'         => 'required',
            'slug'          => 'required|unique:products',
            'price'         => 'required|numeric',
            'sku'           => 'required|unique:products',
            'track_qty'     => 'required|in:Yes,No',
            'category'      => 'required|numeric',
            'is_featured'   => 'required|in:Yes,No',
        ];
        if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

            $product = new Product();
            $product->title       = $request->title;
            $product->slug        = $request->slug;
            $product->description = $request->description;
            $product->price       = $request->price;
            $product->compare_price   = $request->compare_price;
            $product->sku             = $request->sku;
            $product->barcode         = $request->barcode;
            $product->track_qty       = $request->track_qty;
            $product->qty             = $request->qty;
            $product->status          = $request->status;
            $product->category_id     = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id        = $request->product_brand;
            $product->is_featured     = $request->is_featured;
            $product->save();

            // SAVE IMAGE IN PRODUCT/THUMP FOLDER AND FULL-IMG FOLDER
            if (!empty($request->image_array)) {

                foreach ($request->image_array as $temp_img_id) {
                    $temp_image_info  = TempImage::find($temp_img_id);
                    $ext_array        = explode('.', $temp_image_info->name);
                    $extension        = end($ext_array);

                    $product_image   = new ProductImage();
                    $product_image->product_id  = $product->id;
                    $product_image->image       = 'NULL';
                    $product_image->save();

                    $image_name = $product->id . '-' . $product_image->id . '-' . date('YmdHis') . '.' . $extension;

                    $product_image->image  = $image_name;
                    $product_image->save();

                    //GENERATE THUMP IMAGE
                    //FULL IMAGE
                    $source_path = public_path()
                        . '/temp-img/'
                        . $temp_image_info->name;
                    $destination_path = public_path()
                        . '/upload-img/product/full-img/'
                        . $image_name;

                    $image = Image::make($source_path);
                    $image->resize(1200, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });

                    $image->save($destination_path);

                    //THUMB SMALL IMG
                    $destination_path = public_path()
                        . '/upload-img/product/thumb/'
                        . $image_name;

                    $image = Image::make($source_path);
                    $image->fit(150, 150);
                    $image->save($destination_path);
                }
            }

            Session::flash('success', 'Product added successfully');
            return response()->json([
                'status' => true,
                'message' => 'Product added successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    //TO EDIT DATA
    public function edit($id, Request $request)
    {
        $product = Product::find($id);
        $sub_categories = SubCategory::where('category_id',$product->category_id)
                            ->get();

        $data = [];
        $data['sub_categories'] = $sub_categories;
        $data['product']        = $product;
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands     = Brand::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        $data['brands']     = $brands;
        return view('admin.products.edit', $data);
    }
    //TO UPDATE DATA
    public function update($id, Request $request)
    {

    }
    //TO DELETE DATA
    public function destory($id, Request $request)
    {
        
    }
}
