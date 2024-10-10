<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    
    public function login(Request $request)
    {
   
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
          
            $user = Auth::user();

            return response()->json([
                'message' => 'Login successful!',
                'user' => $user,
             
            ], 200);
        }

        return response()->json([
            'message' => 'Invalid email or password',
        ], 401);
    }

//unused
    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json([
            'message' => 'Successfully logged out'
        ], 200);
    }
}
