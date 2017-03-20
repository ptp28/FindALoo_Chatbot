<?php
namespace App\Http\Controllers\Auth;

// use App\User;
//use JWTAuth;
// use App\Http\Controllers\Controller;
// use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\Logins;
use Validator;
// use Illuminate\Http\Request;
use Auth;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuthExceptions\JWTException;
use \Tymon\JWTAuth\Middleware\GetUserFromToken;

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
                $data=array("status"=>"fail","data"=>null, "message"=>"Invalid Credentials");
                return response()->json($data);
                //return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            $data=array("status"=>"error","data"=>null, "message"=>"Could not create token");
            return response()->json($data);
            //return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good, check further
        $user=Logins::where('user_name');
        $userActive= Auth::user()->active;
        if($userActive==-2){
            $data=array("status"=>"fail","data"=>null, "message"=>"Your account is yet to be verified. Please click on the \'Activate\' button in the Registration email sent to you.");
            return response()->json($data);
        }
        if($userActive==0){
            $data=array("status"=>"fail","data"=>null, "message"=>"You have tried to reset your password. But seems like you forgot to set your new password. Please check your reset password e-mail.");
            return response()->json($data);
        }
            
        $logincount=Logins::where('username',$request->email)->first();
        $logincount->login_count=$logincount->login_count+1;
        $logincount->last_login=new DateTime;
        $logincount->save();
        $data=array("status"=>"success","data"=>$token, "message"=>"Successfully logged in");
        return response()->json($data);
        // return response()->json(compact('token'));
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

    //get user from the token
    // public function getAuthenticatedUser(Request $request){
    //     //return $request;
    //     return $url = route('getAuthenticatedUser');
    //     try{
    //         if(! $user=JWTAuth::parseToken()->authenticate()){
    //             return response()->json('user_not_found');
    //         }
    //     }
    //     catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

    //         return response()->json(['token_expired'], $e->getStatusCode());

    //     } 
    //     catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

    //         return response()->json(['token_invalid'], $e->getStatusCode());

    //     } 
    //     catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

    //         return response()->json(['token_absent'], $e->getStatusCode());

    //     }
    //     return response()->json(compact('user'));

    // }
}
