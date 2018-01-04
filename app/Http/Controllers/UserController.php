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

    public function oldcreate(Request $request){

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
            $userdetails->role=3;//3 is the normal user, 2 is moderator, 1 is admin
            $userdetails->save();
            //adding login credentials
            $userlogin=new Logins;
            $userlogin->username=$request->user_email;
            $userlogin->password=Hash::make($request->user_password);
            $userlogin->role=3;
            $userlogin->active=-2;//change it later, -2 for deactivated
            $userlogin->verify_token=$v_token;//use it for sending confirmation mail
            $userlogin->save();
            
            $user_email=$request->user_email;//to prevent serialisation error


            //Send mail to user for confirmation
            Mail::queue('email.user_invite', ['email' => $request->user_email, 'token' => $v_token, 'password' => $request->user_password, 'name' => ucwords(strtolower($request->user_name))], function($message) use ($user_email)
            {
                
                $message
                ->to($user_email)
                ->cc('jayantjnp@gmail.com')
                ->subject('Registration: FindaLoo')
                ->from('admin@e-yantra.org', 'e-Yantra IITB');
            });

           
        });//end of transaction
        $data=array("status"=>"success","data"=>null, "message"=>"Thank you for registering! A confirmation email has been sent to your registered email id");
        //$message=array("success"=>'Thank you for registering! A confirmation email has been sent to your registered email id.');
        return json_encode($data);
    }//end of create


    /**
     *  for creating a new user.
     *
     * @return \Illuminate\Http\Response
     */

    public function create(Request $request){

        $validator = Validator::make($request->all(), [
            'user_name' => 'required|min:5|regex:/(^[A-Za-z. ]+$)+/',
            'user_email' => 'required|email|unique:login,username,'.($request->g_user_id ? ",g_user_id,g_user_id," : ''),
            'g_user_id' => 'required|min:4|unique:login,g_user_id,'.($request->user_email ? ",username,username," : ''),
            'user_fcm' => 'required'
            // .($request->g_user_id ? ",g_user_id,g_user_id," : '')
        ]);

        if($validator->fails()) {
             Log::info("Create function called3");
            // $failedRules = $validator->failed();
            // if(isset($failedRules['user_email']['Unique'])) {
            //     $data=array("status"=>"success","data"=>null, "message"=>"You have succesfully logged in to the application.");
            //     return json_encode($data);
            // }
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"Registration failed");
            return json_encode($data);
        }

        $existing = Logins::where('g_user_id',$request->g_user_id)->first();
        Log::info("current username".print_r($existing,true));
        if(count($existing) > 0){
        if($existing->username == $request->user_email){

            if($existing->fcm_token != $request->user_fcm){
                $existing->fcm_token = $request->user_fcm;
                $existing->save();
            }
            
            $data=array("status"=>"success","data"=>null, "message"=>"You have succesfully logged in to the application.");
            return json_encode($data);
        }
        }

        DB::transaction(function($request) use ($request){

            //adding user details
            // $v_token = str_random(50);
            $userdetails=new UserRegister;
            $userdetails->name=$request->user_name;
            $userdetails->email=$request->user_email;
            // $userdetails->contact=$request->g_user_id;
            // $userdetails->gender=$request->user_gender;
            // $userdetails->age=$request->user_age;
            $userdetails->role=3;//3 is the normal user, 2 is moderator, 1 is admin
            $userdetails->save();

            Log::info("Entry made into the Register for".$request->user_email);
            //adding login credentials
            $userlogin=new Logins;
            $userlogin->username=$request->user_email;
            $userlogin->g_user_id= $request->g_user_id;
            $userlogin->role=3;
            $userlogin->active=1;//change it later, -2 for deactivated
            $userlogin->fcm_token=$request->user_fcm;//use it for sending confirmation mail
            $userlogin->save();
            
            Log::info("Entry made into the Login table for".$request->user_email);

            $user_email=$request->user_email;//to prevent serialisation error


            //Send mail to user for confirmation
            Mail::queue('email.user_invite', ['email' => $request->user_email, 'token' => 'test', 'password' => 'test', 'name' => ucwords(strtolower($request->user_name))], function($message) use ($user_email)
            {
                
                $message
                ->to($user_email)
                ->cc('tusharshahsp@gmail.com')
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
        $user=Logins::where(['verify_token'=>$token,'active'=>-2])->first();
        if(!$user){
            $data=array("status"=>"fail","data"=>null, "message"=>"User not found/Wrong token");
            return response()->json($data);
        }
        $user->active=1;
        $user->verify_token=null;
        $user->save();
        $data=array("status"=>"success","data"=>null, "message"=>"Email verified. You can login now");
        return response()->json($data);
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



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sendNotification($id)
    {
        //

        $url = 'https://fcm.googleapis.com/fcm/send';

        $server_key = 'AAAApmy66Lo:APA91bEo96KuCqgYrFczDOB-xixV_3LsVElSGsPnuU43RdeblTdDwcI_yX3RSreqLIwTJSXeX0fHpJ88KKFTTG_SQbf_h9TSGFrH19xA4HYIPEhQWN4XvbY1DRLJa4OZH7676HLSMs6E';

        $headers = array(
                'Content-Type:application/json',
                'Authorization:key=' . $server_key
        );

        $message = array(
        //use 'registration_ids' instead of 'to' for array of ids and change $reg_id to that id
                'registration_ids' => $fcm_array,
                'priority' => "high",
            //'notification' => array(
            //        "title" => "ALERT",
            //        "body" => "Individual in danger"),
            'data' => array(
                "Title" => $fcm_title,
                "message" => $fcm_message
            )
            );

        $ch = curl_init();

        curl_setopt_array($ch, array(
                CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => json_encode($message)
        ));

        $response = curl_exec($ch);
        curl_close($ch);
            
    }



}
