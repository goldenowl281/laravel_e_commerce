<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        return view('client.account.login');
    }
    public function register()
    {
        return view('client.account.register');
    }

    public function  processRegister(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:1',
            'email' => 'required|email|unique:users',
            'phone' => 'required|numeric',
            'password' => 'required|min:5|confirmed',
        ]);

        if ($validator->passes()) {

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            Session::flash('success', 'Register success');

            return response()->json([
                'status' => true,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);
        }
    }

    public function processLogin (Request $request)
    {
        
    }
}
