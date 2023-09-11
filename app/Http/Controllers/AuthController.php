<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function processLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->passes()) {

            if (Auth::attempt(
                [
                    'email' => $request->email,
                    'password' => $request->password
                ],
                $request->get('remember')
            )) {
                return redirect()->route('client.profile')
                    ->with('success', 'Login success');
            } else {
                return redirect()->route('client.login')
                    ->withInput($request->only('email'))
                    ->with('error', 'Either email/password is invalid');
            }
        } else {
            return redirect()->route('client.login')
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }
    }

    public function profile()
    {
        return view('client.account.profile');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('client.login')
                         ->with('success', 'you logged out');
    }
}
