<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth; 
use App\Models\User; 
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller 
{
    /** 
     * Register API 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422); 
        }
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
    
        return response()->json([
            'status' => 'success',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], 201); 
    }
    
    /** 
     * Login API 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            return response()->json([
                'status' => 'success',
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ], 200);
        } else {
            return response()->json(['error' => 'Invalid email or password.'], 401);
        }
    }

    /** 
     * Get Authenticated User
     * 
     * @return \Illuminate\Http\Response 
     */
    public function user(Request $request)
    {
        return response()->json(['status' => 'success', 'data' => $request->user()], 200);
    }

    /** 
     * Logout API 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json(['status' => 'success', 'message' => 'Logged out successfully.']);
    }
}
