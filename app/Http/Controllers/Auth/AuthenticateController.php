<?php
namespace App\Http\Controllers\Auth;

// use App\User;
//use JWTAuth;
// use App\Http\Controllers\Controller;
// use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\Logins;
use Validator;
// use Illuminate\Http\Request;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuthExceptions\JWTException;

class AuthenticateController extends Controller
{
    public function authenticate(Request $request)
    {
        // return $request->email;
        // grab credentials from the request
        // $users = Logins::all();
        // return $users; 
        // $credentials = $request->only('email', 'password');
        // return $credentials;
        $credentials=array("username"=>$request->email,"password"=>$request->password);
        // return json_encode($credentials);
        // $token = JWTAuth::attempt(['username' => $request->email, 'password' => $request->password]);
        // return response()->json(compact('token'));
        // return $credentials;
        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
    }
    public function login(){
        // $data = Notice::all();
        // return view('register/login')->with('notice', $data);
        return view('register/login');
    }
    public function logout(){
        // $data = Notice::all();
        // return view('register/login')->with('notice', $data);
        return json_encode(JWTAuth::getToken());
        JWTAuth::invalidate(JWTAuth::getToken());
    }
}
