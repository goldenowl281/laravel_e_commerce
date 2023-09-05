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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
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
            $product->price       = $request->price;
            $product->description = $request->description;
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
            $product->short_description = $request->short_description;
            $product->shipping_return   = $request->shipping_return;
            $product->related_products  = (!empty($request->related_products)) ?
                                            implode(',', $request->related_products
                                            ): '';
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

        if (empty($product)) {
            return redirect()->route('products.index')->with('error', "Product not found");
        }
            //FETCH PRODUCT IMAGES
        $product_images = ProductImage::where('product_id', $product->id)->get();

        //  FETCH RELATED PRODUCT
        $related_products = [];
        if ($product->related_products != '') {
            $product_arr = explode(',', $product->related_products);
            $related_products = Product::whereIn('id', $product_arr)->get();
        }




        $sub_categories = SubCategory::where('category_id', $product->category_id)
            ->get();
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands     = Brand::orderBy('name', 'ASC')->get();

        $data = [];
        $data['sub_categories'] = $sub_categories;
        $data['product']        = $product;
        $data['categories']     = $categories;
        $data['brands']         = $brands;
        $data['product_images'] = $product_images;
        $data['related_products'] = $related_products;
        return view('admin.products.edit', $data);
    }
    //TO UPDATE DATA
    public function update($id, Request $request)
    {

        $product = Product::find($id);


        $rules = [
            'title'         => 'required',
            'slug'          => "required|unique:products,slug,$product->id,id",
            'price'         => 'required|numeric',
            'sku'           => "required|unique:products,sku,$product->id,id",
            'track_qty'     => 'required|in:Yes,No',
            'category'      => 'required|numeric',
            'is_featured'   => 'required|in:Yes,No',
        ];
        if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

            $product->title       = $request->title;
            $product->slug        = $request->slug;
            $product->price       = $request->price;
            $product->description = $request->description;
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
            $product->short_description = $request->short_description;
            $product->shipping_return   = $request->shipping_return;
            $product->related_products  = (!empty($request->related_products)) ?
                                            implode(',', $request->related_products
                                            ): '';
            $product->save();

            // SAVE IMAGE IN PRODUCT/THUMP FOLDER AND FULL-IMG FOLDER
            // if (!empty($request->image_array)) {

            //     foreach ($request->image_array as $temp_img_id) {
            //         $temp_image_info  = TempImage::find($temp_img_id);
            //         $ext_array        = explode('.', $temp_image_info->name);
            //         $extension        = end($ext_array);

            //         $product_image   = new ProductImage();
            //         $product_image->product_id  = $product->id;
            //         $product_image->image       = 'NULL';
            //         $product_image->save();

            //         $image_name = $product->id . '-' . $product_image->id . '-' . date('YmdHis') . '.' . $extension;

            //         $product_image->image  = $image_name;
            //         $product_image->save();

            //         //GENERATE THUMP IMAGE
            //         //FULL IMAGE
            //         $source_path = public_path()
            //             . '/temp-img/'
            //             . $temp_image_info->name;
            //         $destination_path = public_path()
            //             . '/upload-img/product/full-img/'
            //             . $image_name;

            //         $image = Image::make($source_path);
            //         $image->resize(1200, null, function ($constraint) {
            //             $constraint->aspectRatio();
            //         });

            //         $image->save($destination_path);

            //         //THUMB SMALL IMG
            //         $destination_path = public_path()
            //             . '/upload-img/product/thumb/'
            //             . $image_name;

            //         $image = Image::make($source_path);
            //         $image->fit(150, 150);
            //         $image->save($destination_path);
            //     }
            // }

            Session::flash('success', 'Product updated successfully');
            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    //TO DELETE DATA
    public function destory($id, Request $request)
    {
        $product = Product::find($id);

        if (empty($product)) {
            Session::flash('error', "Product not found to delete");
            return response()->json([
                'status' => false,
                'not_found' => true,
                'message' => "Your input product data not found"
            ]);
        }

        $product_images = ProductImage::where('product_id', $id)->get();

        if (!empty($product_images)) {

            foreach ($product_images as $image) {
                File::delete(public_path('upload-img/product/thumb/' . $image->image));
                File::delete(public_path('upload-img/product/full-img/' . $image->image));
            }
            ProductImage::where('product_id', $id)->delete();
        }

        $product->delete();

        Session::flash('success', 'Product deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'product deleted successfully'
        ]);
    }

    //TO GET RELATED DATA
    public function get (Request $request)
    {
        if ($request->term != '') {
            $products = Product::where('title', 'like','%'.$request->term.'%')
                            ->get();

            if ($products != NULL) {
                foreach ($products as $product) {
                    $temp_product[] = array('id'=> $product->id,
                                            'text'=> $product->title);
                }
            }
            return response()->json([
                'tags' => $temp_product,
                'status'=> true
            ]);
        }
    }
}
