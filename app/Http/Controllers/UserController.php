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

use App\Http\Requests\RegisterUserValidate;
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
     * Show the form for creating a new user.
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
            return json_encode($validator->errors());
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
            $userlogin->active=1;//change it later, -2 for deactivated
            $userlogin->verify_token=$v_token;//use it for sending confirmation mail
            $userlogin->save();
            


            // //Send mail to user for confirmation
            // Mail::queue('email.team_member_invite', ['email' => $request->user_email, 'token' => $v_token_mem1, 'password' => $mem1_pwd, 'name' => ucwords(strtolower($request->teamMember1_first_name)), 'team_id' => $teamId], function($message) use ($mem1_email)
            // {
            //     $message
            //     ->to($mem1_email)
            //     ->cc('admin@e-yantra.org')
            //     ->subject('Registration: FindaLoo')
            //     ->from('admin@e-yantra.org', 'e-Yantra IITB');
            // });

           
        });//end of transaction

     // return redirect('register')->with('success','Thank you for registering! A confirmation email has been sent to each team member. Each team member should click on the Activation link to activate their account. Follow the validation instructions as specified in confirmation e-mail to complete the registration process.');
        $message=array("success"=>'Thank you for registering! A confirmation email has been sent to your registered email id.');
        return json_encode($message);
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
        $username=$user->username;
        return response()->json(compact('user'));

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
    public function update(Request $request, $id)
    {
        //
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
