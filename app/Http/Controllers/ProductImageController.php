<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    // TO STORE IMAGE TO DATABASE AND PRODUCT THUMB FILE
    public function update(Request $request)
    {
        $product_id = $request->product_id;
        $image      = $request->image;
        $extension  = $image->getClientOriginalExtension();
        $source_path       = $image->getPathName();

        $product_image   = new ProductImage();
        $product_image->product_id  = $product_id;
        $product_image->image       = 'NULL';
        $product_image->save();

        $image_name = $product_id . '-' . $product_image->id . '-' . date('YmdHis') . '.' . $extension;

        $product_image->image  = $image_name;
        $product_image->save();

        //GENERATE THUMP IMAGE
        //FULL IMAGE

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

        return response()->json([
            'status'        => true,
            'image_id'      => $product_image->id,
            'image_path'    => asset('upload-img/product/thumb/' . $product_image->image),
            'message' => 'image upload successfully'
        ]);
    }
    //TO DELETE IMAGES FROM DB
    public function destory(Request $request)
    {
        $product_image = ProductImage::find($request->id);

        if (empty($product_image)) {
            return response()->json([
                'status' => false,
                'message'=> 'Image not found'
            ]);
        }

        File::delete(public_path('upload-img/product/full-img/'.$product_image->image));
        File::delete(public_path('upload-img/product/thumb/'.$product_image->image));

        $product_image->delete();

        return response()->json([
            'status' => true,
            'message'=> "Image deleted successfully"
        ]);
    }
}
