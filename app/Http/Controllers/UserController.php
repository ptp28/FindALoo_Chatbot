<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use Mail;
use DB;
use DateTime;
use Log;

use JWTAuth;
use Tymon\JWTAuthExceptions\JWTException;

use App\Models\Logins;
use App\Models\UserRegister;
use Hash;

class UserController extends Controller
{
    // public function __construct()
    // {
    //    // Apply the jwt.auth middleware to all methods in this controller
    //    // except for the authenticate method. We don't want to prevent
    //    // the user from retrieving their token if they don't already have it
    //    $this->middleware('jwt-auth');
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     *  for creating a new user.
     *
     * @return \Illuminate\Http\Response
     */

    public function create(Request $request){

        $validator = Validator::make($request->all(), [
            'user_name' => 'required|min:5|regex:/(^[A-Za-z. ]+$)+/',
            'user_email' => 'required|email|unique:login,username',
            'user_contact' => 'required|digits:10|unique:User_Register,contact',
            'user_password' => 'required|min:5'
        ]);

        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"Registration failed");
            return json_encode($data);
        }

        DB::transaction(function($request) use ($request){

            //adding user details
            $v_token = str_random(50);
            $userdetails=new UserRegister;
            $userdetails->name=$request->user_name;
            $userdetails->email=$request->user_email;
            $userdetails->contact=$request->user_contact;
            $userdetails->gender=$request->user_gender;
            $userdetails->age=$request->user_age;
            $userdetails->role=1;
            $userdetails->save();
            //adding login credentials
            $userlogin=new Logins;
            $userlogin->username=$request->user_email;
            $userlogin->password=Hash::make($request->user_password);
            $userlogin->role=1;
            $userlogin->active=-2;//change it later, -2 for deactivated
            $userlogin->verify_token=$v_token;//use it for sending confirmation mail
            $userlogin->save();
            
            $user_email=$request->user_email;//to prevent serialisation error


            //Send mail to user for confirmation
            Mail::queue('email.user_invite', ['email' => $request->user_email, 'token' => $v_token, 'password' => $request->user_password, 'name' => ucwords(strtolower($request->user_name))], function($message) use ($user_email)
            {
                
                $message
                ->to($user_email)
                ->cc('admin@e-yantra.org')
                ->subject('Registration: FindaLoo')
                ->from('admin@e-yantra.org', 'e-Yantra IITB');
            });

           
        });//end of transaction
        $data=array("status"=>"success","data"=>null, "message"=>"Thank you for registering! A confirmation email has been sent to your registered email id");
        //$message=array("success"=>'Thank you for registering! A confirmation email has been sent to your registered email id.');
        return json_encode($data);
    }//end of create

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
        // $users = Logins::all();
        // return $users;
        $user = JWTAuth::parseToken()->authenticate();
        if($user->role==3)//list users for admin
        {
            $userdetails=UserRegister::all();//getting user details
            //$userdetails=$userdetails[0];
            $data=array("status"=>"success","data"=>$userdetails, "message"=>"User details fetched");
            return response()->json($data);
        }
        else if($user->role==2)
        {
            return "Moderator goes here";
        }
        //normal user goes here
        $userdetails=UserRegister::where(['email'=>$user->username])->get();//getting user details
        $userdetails=$userdetails[0];
        $data=array("status"=>"success","data"=>$userdetails, "message"=>"User details fetched");
        return response()->json($data);
        

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
    }
     /**
     * Activate User account by verifying token
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function verify_account($token)
    {
        if(!$token){
            $data=array("status"=>"error","data"=>null, "message"=>"Empty token passed");
            return response()->json($data);
        }
        $user=Logins::where(['verfiy_token'=>$token,'active',-2])->first();
        if($user){
            $data=array("status"=>"fail","data"=>null, "message"=>"User not found/Wrong token");
            return response()->json($data);
        }
        $user->active=1;
        $user->verify_token=null;
        $user->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
