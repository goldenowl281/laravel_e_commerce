<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class TempImagesController extends Controller
{
    public function create(Request $request)
    {
        $image = $request->image;

        if (!empty($image)) {
            $extension = $image->getClientOriginalExtension();
            $newExtName = time() . '.' . $extension;

            $tempImage = new TempImage();
            $tempImage->name = $newExtName;
            $tempImage->save();

            $image->move(public_path() . '/temp-img', $newExtName);

            //Generate thumbnail
            $sourcePath = public_path() . '/temp-img/' . $newExtName;
            $destinationPath = public_path() . '/temp-img/thumb/' . $newExtName;
            $image = Image::make($sourcePath);
            $image->fit(150, 150);
            $image->save($destinationPath);

            return response()->json([
                'status'     => true,
                'image_id'   => $tempImage->id,
                'image_path' => asset('/temp-img/thumb/' . $newExtName),
                'message'    => 'Image uploaded successfully'
            ]);
        }
    }
}
