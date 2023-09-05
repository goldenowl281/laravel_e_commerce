<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart_content = Cart::content();
        // dd($cart_content);
        $data['cart_content'] = $cart_content;
        return view('client.cart', $data);
    }

    public function addToCart(Request $request)
    {
        $product = Product::with('product_images')->find($request->id);

        if ($product == NULL) {
            return response()->json([
                'status'  => false,
                'message' => 'products data not found'
            ]);
        }

        if (Cart::count() > 0) {
            //PRODUCT FOUND IN CART
            //Check if this cart product alreday exit in cart
            //if(cart aleready exit) return a message product already exit
            //else add product in cart
            $cart_content = Cart::content();
            $product_exit = false;

            foreach ($cart_content as $item) {
                if ($item->id == $product->id) {
                    $product_exit = true;
                }
            }

            if ($product_exit == false) {
                Cart::add(
                    $product->id,
                    $product->title,
                    $product->qty,
                    $product->price,
                    [
                        'product_img' => (!empty($product->product_images)) ?
                            $product->product_images->first() : ''
                    ]
                );
                $status  = true;
                $message = $product->title . " added  successfully";
            } else {
                $status  = false;
                $message = $product->title . " already  exit";
            }
        } else {
            //CART IS EMPTY
            //
            Cart::add(
                $product->id,
                $product->title,
                1,
                $product->price,
                [
                    'product_img' => (!empty($product->product_images)) ?
                        $product->product_images->first() : ''
                ]
            );
            $status  = true;
            $message = $product->title . " added  in first cart";
        }
        return response()->json([
            'status'  => $status,
            'message' => $message
        ]);
    }
}
