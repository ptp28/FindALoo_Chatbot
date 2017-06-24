<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use Mail;
use DB;
//use DateTime;
use Log;
//auth
use JWTAuth;
use Tymon\JWTAuthExceptions\JWTException;
//tables
use App\Models\Logins;
use App\Models\ToiletRegister;
use App\Models\UserRegister;
use App\Models\ToiletImages;
use App\Models\ToiletFeedback;
use App\Models\ToiletVisits;
use App\Models\MSDPToiletRegister;
//image
use Image;
use File;
use Storage;
class ToiletController extends Controller
{
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
     * create new toilet entry
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'toilet_lat' => 'required|min:5',
            'toilet_lng' => 'required|min:5',
            //'toilet_ward' => 'required|email|unique:login,username',
           // 'toilet_address' => 'required|digits:10|unique:User_Register,contact',
           // 'toilet_organisation' => 'required|min:5'
        ]);

        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"Toilet Registration failed");
            return json_encode($data);
        }

        DB::transaction(function($request) use ($request){

            //adding toilet details
            $user = JWTAuth::parseToken()->authenticate();//finding the user from the token
            $userid=UserRegister::where(['email'=>$user->username])->pluck('id');//getting user_id

            $toiletdetails=new ToiletRegister;
            $toiletdetails->lat=$request->toilet_lat;
            $toiletdetails->lng=$request->toilet_lng;
            $toiletdetails->user_id=$userid;
            if($request->toilet_ward!=null)
                $toiletdetails->ward=$request->toilet_ward;
            if($request->toilet_address!=null)
                $toiletdetails->address=$request->toilet_address;
            if($request->toilet_organisation!=null)
                $toiletdetails->organisation=$request->toilet_organisation;
            if($request->toilet_ownership!=null)
                $toiletdetails->ownership=$request->toilet_ownership;
            $toiletdetails->save();
            


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
        $data=array("status"=>"success","data"=>null, "message"=>"Thank you for registering the new Toilet! It will be visible on the map after verification");
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
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function showOld(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_lat' => 'required|min:5',
            'user_lng' => 'required|min:5'
        ]);
        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"wrong request");
            return json_encode($data);
        }
        $rad=0;
        if($request->rad==null)
            $rad=1.5;//keeping default distance as 1.5 km
        else
            $rad=$request->rad;
        $user_lat=$request->user_lat;
        $user_lng=$request->user_lng;
         //
        // $user = JWTAuth::parseToken()->authenticate();
        // if($user->role==3)//list toilets for admin
        // {
        //     // $toiletdetails = ToiletRegister::all();
        //     // $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilet data fetched");
        //     // return json_encode($data);
        // }
        // if($user->role==2)//list toilets for moderaters
        // {
        //     // $toiletdetails = ToiletRegister::all();
        //     // $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilet data fetched");
        //     // return json_encode($data);
        // }
        $toiletdetails=DB::select('SELECT id,lat,lng,address, ( 6371 * acos( cos( radians(:user_lat1) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(:user_lng) ) + sin( radians(:user_lat2) ) * sin( radians( lat ) ) ) ) AS distance FROM toilets HAVING distance < :rad',['user_lat1'=>$user_lat,'user_lat2'=>$user_lat,'user_lng'=>$user_lng,'rad'=>$rad]);
        if(sizeof($toiletdetails)>0)
        {
            $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilets fetched");
            return json_encode($data);
        }
        $data=array("status"=>"fail","data"=>null, "message"=>"No toilets found in your area");
        return json_encode($data);
    }

/**
     * Display the specified resource.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_lat' => 'required|min:5',
            'user_lng' => 'required|min:5'
        ]);
        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"wrong request");
            return json_encode($data);
        }
        $rad=0;
        if($request->rad==null)
            $rad=1.5;//keeping default distance as 1.5 km
        else
            $rad=$request->rad;
        $user_lat=$request->user_lat;
        $user_lng=$request->user_lng;
         //
        // $user = JWTAuth::parseToken()->authenticate();
        // if($user->role==3)//list toilets for admin
        // {
        //     // $toiletdetails = ToiletRegister::all();
        //     // $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilet data fetched");
        //     // return json_encode($data);
        // }
        // if($user->role==2)//list toilets for moderaters
        // {
        //     // $toiletdetails = ToiletRegister::all();
        //     // $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilet data fetched");
        //     // return json_encode($data);
        // }
        $toiletdetails=DB::select('SELECT OBJECTID,lat,lng,address, ( 6371 * acos( cos( radians(:user_lat1) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(:user_lng) ) + sin( radians(:user_lat2) ) * sin( radians( lat ) ) ) ) AS distance FROM MSDPUSERToilet_Block HAVING distance < :rad',['user_lat1'=>$user_lat,'user_lat2'=>$user_lat,'user_lng'=>$user_lng,'rad'=>$rad]);
        if(sizeof($toiletdetails)>0)
        {
            $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilets fetched");
            return json_encode($data);
        }
        $data=array("status"=>"fail","data"=>null, "message"=>"No toilets found in your area");
        return json_encode($data);
    }

    /**
     * Display the specific toilet IIT.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function showSpecificToiletOld(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'toilet_id' => 'required'
        ]);
        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"wrong request");
            return json_encode($data);
        }
         //
        // $user = JWTAuth::parseToken()->authenticate();
        // if($user->role==3)//list toilets for admin
        // {
        //     // $toiletdetails = ToiletRegister::all();
        //     // $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilet data fetched");
        //     // return json_encode($data);
        // }
        // if($user->role==2)//list toilets for moderaters
        // {
        //     // $toiletdetails = ToiletRegister::all();
        //     // $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilet data fetched");
        //     // return json_encode($data);
        // }
        $toiletdetails=ToiletRegister::where(['id'=>$request->toilet_id])->get();
        if(sizeof($toiletdetails)>0)
        {
            $toiletdetails=$toiletdetails[0];
            $toiletphoto=ToiletImages::where('toilet_id',$request->toilet_id)->select('user_id','image_name','image_title','created_at')->first();
           //$toiletphoto=$toiletphoto[0];
            $ratingsCount=ToiletFeedback::where('toilet_id',$request->toilet_id)->count();
            $toiletdetails->toilet_image=$toiletphoto;
            $toiletdetails->ratingsCount=$ratingsCount;
            $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilet details fetched");
        }
        else
            $data=array("status"=>"fail","data"=>null, "message"=>"invalid toilet id");
        return json_encode($data);
    }

        /**
     * Display the specific toilet.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function showSpecificToilet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'toilet_id' => 'required'
        ]);
        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"wrong request");
            return json_encode($data);
        }
         //
        // $user = JWTAuth::parseToken()->authenticate();
        // if($user->role==3)//list toilets for admin
        // {
        //     // $toiletdetails = ToiletRegister::all();
        //     // $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilet data fetched");
        //     // return json_encode($data);
        // }
        // if($user->role==2)//list toilets for moderaters
        // {
        //     // $toiletdetails = ToiletRegister::all();
        //     // $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilet data fetched");
        //     // return json_encode($data);
        // }
        $toiletdetails=MSDPToiletRegister::where(['OBJECTID'=>$request->toilet_id])->select('OBJECTID','lat','lng','wardheader','address','ORGNAME','ownership','countms','countfs','condition','cleanliness','maintenance' ,'ambience','water','SURVEYEDDA')->get();
        if(sizeof($toiletdetails)>0)
        {
            $toiletdetails=$toiletdetails[0];
            $toiletphoto=ToiletImages::where('toilet_id',$request->toilet_id)->select('user_id','image_name','image_title','created_at')->first();
           //$toiletphoto=$toiletphoto[0];
            $ratingsCount=ToiletFeedback::where('toilet_id',$request->toilet_id)->count();
            $toiletdetails->toilet_image=$toiletphoto;
            $toiletdetails->ratingsCount=$ratingsCount;
            $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilet details fetched");
        }
        else
            $data=array("status"=>"fail","data"=>null, "message"=>"invalid toilet id");
        return json_encode($data);
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
     * API for entering feedback for specific toilet
     *
     * @param  int  Request $request
     * @return \Illuminate\Http\Response
     */
    public function addFeedback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'toilet_id' => 'required',
            'toilet_cleanliness' => 'required',
            'toilet_maintenance' => 'required',
            'toilet_ambience' => 'required'

        ]);
        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"incomplete form");
            return json_encode($data);
        }
        $user = JWTAuth::parseToken()->authenticate();//finding the user from the token
        $userid=UserRegister::where(['email'=>$user->username])->pluck('id');//getting user_id
        $userid=6;
        $checkUser=ToiletFeedback::where(['toilet_id'=>$request->toilet_id, 'user_id'=>$userid])->count();
        if($checkUser==0){
            DB::transaction(function($request) use ($request, $userid){
            // $user = JWTAuth::parseToken()->authenticate();//finding the user from the token
            // $userid=UserRegister::where(['email'=>$user->username])->pluck('id');//getting user_id
            $toiletFeedback=new ToiletFeedback;
            $toiletFeedback->user_id=$userid;
            $toiletFeedback->toilet_id=$request->toilet_id;
            $toiletFeedback->cleanliness=$request->toilet_cleanliness;
            $toiletFeedback->maintenance=$request->toilet_maintenance;
            $toiletFeedback->ambience=$request->toilet_ambience;
            if($request->toilet_comment!=null)
                $toiletFeedback->comment=$request->toilet_comment;
            $toiletFeedback->save();
            //$toilet_id=$request->toilet_id;
            $toiletdetails=DB::select('SELECT AVG(cleanliness) c, AVG(maintenance) m, AVG(ambience) a FROM toilet_feedback where toilet_id=:toilet_id',['toilet_id'=>$request->toilet_id]);
            $toiletdetails=$toiletdetails[0];
            $ratings=ToiletRegister::where('id',$request->toilet_id)->first();
            $ratings->cleanliness=$toiletdetails->c;
            $ratings->maintenance=$toiletdetails->m;
            $ratings->ambience=$toiletdetails->a;
            $ratings->save();
            });//transaction ends here

            $data=array("status"=>"success","data"=>null, "message"=>"Thank you for your Feedback");
            return json_encode($data);
        }
        $data=array("status"=>"fail","data"=>null, "message"=>"Multiple feedbacks not allowed");
        return json_encode($data);
        

    }
    /**
     * photo upload for Toilets
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'toiletPht' => 'required|image:jpeg,bmp,png|max:3000',
            ],

            [
            'toiletPht.required' => 'Please select a photo.',
            'toiletPht.image' => 'Please upload only .jpg/.png/.bmp file.',
            'toiletPht.max' => 'Size of the file should be less than 3MB.',
            // 'toiletPht.dimensions' => 'The image should be of at least 800 x 600 resolution'
        ]);
        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"Photo upload failed");
            return json_encode($data);
        }
        DB::transaction(function($request) use ($request){
            $path = public_path().'/img/toilets/';
            $user = JWTAuth::parseToken()->authenticate();//finding the user from the token
            $userid=UserRegister::where(['email'=>$user->username])->pluck('id');//getting user_id
            $ext = strtolower($request->file('toiletPht')->getClientOriginalExtension());
            $filename = str_random(15).".".$ext;
            $request->file('toiletPht')->move($path, $filename);
            $toiletPhoto=new ToiletImages;
            $toiletPhoto->user_id=$userid;
            $toiletPhoto->toilet_id=$request->toilet_id;
            $toiletPhoto->image_name=$filename;
            if($request->image_title!=null)
                $toiletPhoto->image_title=$request->image_title;
            $toiletPhoto->active=1;
            $toiletPhoto->save();
        });
        $data=array("status"=>"success","data"=>null, "message"=>"Image uploaded");
        return json_encode($data);

    }
    /**
     * for fetching all images
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showImages()
    {
        $ToiletImages = ToiletImages::all();
        $data=array("status"=>"success","data"=>$ToiletImages, "message"=>"All Images fetched");
        return json_encode($data);
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
