<?php
namespace App\Http\Controllers\Auth;

// use App\User;
//use JWTAuth;
// use App\Http\Controllers\Controller;
// use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\Logins;
use App\Models\UserRegister;
use Validator;
// use Illuminate\Http\Request;
use Auth;
use Mail;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuthExceptions\JWTException;
use Hash;
use DB;
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
        $userdetails=UserRegister::where(['email'=>$request->email])->select('name', 'email', 'role','gender')->first();//
        $userdetails->token=$token;
        //getting user_id
        //$userRole=UserRegister::where(['email'=>$user->username])->pluck('role');//getting user role
        $data=array("status"=>"success","data"=>$userdetails, "message"=>"Login Successful");
        return json_encode($data);
        // $data=array("status"=>"success","data"=>$token, "message"=>"Successfully logged in");
        // return response()->json($data);
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

    //forgetpassword
    public function forgotpassProcess(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required|email',
        ]);
        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"Input a valid email id");
            return json_encode($data);
        }
        $username=$request->username;
        $user=Logins::where('username',$username)->first();
        if(!$user){
            $data=array("status"=>"fail","data"=>null, "message"=>"$username is not registered with us");
            return json_encode($data);
        }
        if($user->active == -2){
            $data=array("status"=>"fail","data"=>null, "message"=>"Your e-mail is yet to be verify by clicking on the 'Activate' button in the Registration email sent by FindaLoo");
            return json_encode($data);
        }
        //email verification completed, now prepare for sending an email with token
        /* Generate token and store in DB */
        $token = '';
        if($user->token != '' && !empty($user->token)){
            $token = $user->token;
        } else {
            $token = md5(str_random(50));
            $user->token = $token;
            $user->active = 0;

            if(!$user->save()){
                $data=array("status"=>"error","data"=>null, "message"=>"Unable to save the information. Please contact us at admin@e-yantra.org via email about the issue");
                return json_encode($data);
            }
        }
        Mail::queue('email.forgotPass', ['username' => $username, 'token' => $token], function($message) use ($username)
        {
            
            $message
            ->to($username)
            ->cc('jayantjnp@gmail.com')
            ->subject('Forgot Password: FindaLoo')
            ->from('admin@e-yantra.org', 'e-Yantra IITB');
        });
        $data=array("status"=>"success","data"=>null, "message"=>"Link for confirming new password has been sent on your email");
        return json_encode($data);

    }

    //Verify the password token
    public function verifyPassToken($username, $token) {
        /* Validate username and token */
        $userrecord = Logins::where(['username'=>$username, 'token'=>$token])->first();
        if(!$userrecord || ($userrecord->token != $token)) {
            // Redirect to login page with error message.
            $data=array("status"=>"error","data"=>null, "message"=>"Unable to set new password. Please contact us at admin@e-yantra.org via email about the issue");
            return json_encode($data);
        }
        $newpassword = str_random(8);
        $userrecord->password=Hash::make($newpassword);
        $userrecord->change_count=$userrecord->change_count+1;
        $userrecord->token=null;
        $userrecord->save();
        $credentials=array("username"=>$username,"password"=>$newpassword);
        Mail::queue('email.newPass', ['username' => $username, 'newpassword' => $newpassword], function($message) use ($username)
        {
            
            $message
            ->to($username)
            ->cc('jayantjnp@gmail.com')
            ->subject('New Password: FindaLoo')
            ->from('admin@e-yantra.org', 'e-Yantra IITB');
        });
        $data=array("status"=>"success","data"=>$credentials, "message"=>"New password allotted and a confirmation mail has been also sent");
        return json_encode($data);
    }
    //for normal changing of password
    public function changePass(Request $request){
        $validator = Validator::make($request->all(), [
            'newPassword' => 'required|min:5',
            'repeatPassword' => 'required|min:5'
        ]);
        if ($validator->fails()){
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"Password change failed");
            return json_encode($data);
        }
        $newpassword = $request->newPassword;
        $repeatpassword = $request->repeatPassword;
        if ($newpassword != $repeatpassword){
            $data=array("status"=>"fail","data"=>null, "message"=>"Password, Confirm Password doesn\'t match.");
            return json_encode($data);
        }
        DB::transaction(function($request) use ($request,$newpassword, $repeatpassword ){
            $username=Auth::user()->username;
            $userrecord = Logins::where('username', Auth::user()->username)->first();
            $userrecord->password = Hash::make($newpassword);
            $userrecord->token = null;
            $userrecord->active = 1;
            $userrecord->change_count = $userrecord->change_count + 1;
            $userrecord->save();
            Mail::queue('email.newPass', ['username' => $username, 'newpassword' => $newpassword], function($message) use ($username)
            {
                
                $message
                ->to($username)
                ->cc('jayantjnp@gmail.com')
                ->subject('New Password: FindaLoo')
                ->from('admin@e-yantra.org', 'e-Yantra IITB');
            });
        });
        $data=array("status"=>"success","data"=>null, "message"=>"New password saved");
        return json_encode($data);
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
