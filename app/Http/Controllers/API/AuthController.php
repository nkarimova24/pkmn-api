<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth; 
use App\Models\User; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class AuthController extends Controller 
{
  
   private $apiToken;
   public function __construct()
    {
    $this->apiToken = uniqid(base64_encode(Str::random(40)));
    }
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
              'token' => $user->createToken('API Token')->plainTextToken,
              'name' => $user->name,
          ],
      ]); 
  }
    public function login(Request $request)
  {
 
      $validator = Validator::make($request->all(),[
          'email' => 'required|email',
          'password' => 'required',
      ]);

      if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }


    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials, $request->remember)) {

        return redirect()->intended('/');
    }

    return redirect()->back()->withErrors(['email' => 'Invalid email or password.']);
}
}