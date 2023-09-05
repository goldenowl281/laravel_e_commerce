<?php

namespace App\Http\Controllers\shop;

use App\Http\Controllers\Controller;
use App\Models\admin\Brand;
use App\Models\admin\SubCategory;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(
        Request $request,
        $category_slug = null,
        $sub_category_slug = null
    ) {
        $category_selected      = '';
        $sub_category_selected  = '';

        $categories  =  Category::orderBy('name', 'ASC')
            ->with('subCategory')
            ->where('status', 1)
            ->get();

        $brands   = Brand::orderBy('name', 'ASC')
            ->where('status', 1)
            ->get();

        $products = Product::where('status', 1);

        // Apply Filters Here
        if (!empty($category_slug)) {
            $category = Category::where('slug', $category_slug)->first();
            $products = $products->where('category_id', $category->id);
            $category_selected   = $category->id;
        }

        if (!empty($sub_category_slug)) {
            $sub_category = SubCategory::where('slug', $sub_category_slug)
                ->first();
            $products = $products->where('sub_category_id', $sub_category->id);
            $sub_category_selected = $sub_category->id;
        }

        $brands_array   = [];
        if (!empty($request->get('brand'))) {
            $brands_array   = explode(',', $request->get('brand'));
            $products       = $products->whereIn('brand_id', $brands_array);
        }

        $price_min = $request->get('price_min');
        $price_max = $request->get('price_max');
        if ($price_min != '' && $price_max != '') {
            $products = $products->whereBetween('price', [intval($price_min), intval($price_max)]);
        }

        $sort = $request->get('sort');
        if ($sort != '') {
            if ($sort == 'latest') {
                $products = $products->orderBy('id', 'DESC');
            } else if ($sort == 'price_asc') {
                $products = $products->orderBy('price', 'ASC');
            } else if ($sort == 'price_desc') {
                $products = $products->orderBy('price', 'DESC');
            }
        } else {
            $products = $products->orderBy('id', 'DESC');
        }
        $products = $products->paginate(3);

        $data['categories'] = $categories;
        $data['brands']     = $brands;
        $data['products']   = $products;
        $data['sort']       = $sort;
        $data['price_min']  = intval($price_min);
        $data['price_max']  = (intval($price_max) == 0) ? 10000 : $price_max;
        $data['sub_category_selected']   = $sub_category_selected;
        $data['category_selected']       = $category_selected;
        $data['brands_array']            = $brands_array;


        return view('client.shop', $data);
    }

    public function product ($slug)
    {
        $product = Product::where('slug', $slug)->with('product_images')->first();

        if ($product == NULL) {
            abort(404);
        }

         //  FETCH RELATED PRODUCT
         $related_products = [];
         if ($product->related_products != '') {
             $product_arr = explode(',', $product->related_products);
             $related_products = Product::whereIn('id', $product_arr)
                                    ->with('product_images')
                                    ->get();
         }

        $data = [];
        $data['product'] = $product;
        $data['related_products'] = $related_products;



        return view('client.product', $data);
    }
}
