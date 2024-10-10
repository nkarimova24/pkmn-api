<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    // Show registration form (not usually needed in API context)
    public function index() {
        return view("register.index");
    }

    // Handle registration
    public function create(Request $request) {
        try {
            // Validate the request data
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:users,name',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);
    
            // Create the user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']), 
            ]);
    
            // Log the user in
            Auth::login($user);
    
            // Return a success response
            return response()->json(['message' => 'Account created successfully!', 'user' => $user], 201);
    
        } catch (\Exception $e) {
            // Log the error for debugging
           
    
            // Return a detailed error response
            return response()->json(['error' => 'Error registering user: ' . $e->getMessage()], 500);
        }
    }
    
}
