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
use App\Models\ToiletReq;
use App\Models\MSDPToiletRegister;
use App\Models\ReportIssues;
use App\Models\General_Feedback;
use App\Models\Comment;
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
    public function requestReg(Request $request){

        // Log::info("datat ".print_r($request->all(), true));

        $validator = Validator::make($request->all(), [
            'toilet_lat' => 'required|min:5',
            'toilet_lng' => 'required|min:5',
            'toilet_name' => 'required',
            'g_user_id'   => 'required',
            'time_open'   => 'required',
            'time_close'   => 'required',
            'free'   => 'required',
            'dr_water'   => 'required',
            'men'   => 'required',
            'women'   => 'required',
            'disabled'   => 'required',
            'western'   => 'required',
            'indian'   => 'required',
            'napkin'   => 'required',
            'clean'  => 'required',
            'maintenance'  => 'required',
            'ambience'  => 'required',
            'safety'  => 'required',
            'water'  => 'required'
           // 'toilet_address' => 'required|digits:10|unique:User_Register,contact',
           // 'toilet_organisation' => 'required|min:5'
        ]);
        $toilet_id = -1;

        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"Toilet Registration failed");
            return json_encode($data);
        }

        $user = Logins::where('g_user_id',$request->g_user_id)->first();//finding the user from the token
        Log::info("datacxcxcxcx ".print_r($user,true));
        $userdetail=UserRegister::where(['email'=>$user->username])->first();//getting user_id
        Log::info("request data 1".print_r($userdetail,true));
        // $user = JWTAuth::parseToken()->authenticate();//finding the user from the token
        // $userRole=$user->role;//getting user role
        // if($userRole!=1 && $userRole!=2)
        // {
        //     $data=array("status"=>"fail","data"=>null, "message"=>"Only Admin or Moderator can register new toilet place");
        //     return json_encode($data);
        // }
        
        // $userid=UserRegister::where(['email'=>$user->username])->pluck('id');//getting user_id
         try{
            $exception=DB::transaction(function() use ($request, $userdetail){
                
                
                // Log::info("Request Data ".print_r($request,true));

            // $toiletdetails=new ToiletRegister;
            // $toiletdetails->lat=$request->toilet_lat;
            // $toiletdetails->lng=$request->toilet_lng;
            // $toiletdetails->user_id=$userid;
            // if($request->toilet_ward!=null)
            //     $toiletdetails->ward=$request->toilet_ward;
            // if($request->toilet_address!=null)
            //     $toiletdetails->address=$request->toilet_address;
            // if($request->toilet_organisation!=null)
            //     $toiletdetails->organisation=$request->toilet_organisation;
            // if($request->toilet_ownership!=null)
            //     $toiletdetails->ownership=$request->toilet_ownership;
            // $toiletdetails->save();
                Log::info("request data inside controller ".print_r($request->all(), true));
                $toiletdetails=new MSDPToiletRegister;
                $toiletdetails->lat=$request->get('toilet_lat');
                $toiletdetails->lng=$request->get('toilet_lng');
                $toiletdetails->USERID=$userdetail->id;
                if($request->toilet_name!=null)
                    $toiletdetails->NAME=$request->get('toilet_name');
                if($request->toilet_address!=null)
                    $toiletdetails->ADDRESS=$request->get('toilet_address');
                if($request->toilet_organisation!=null)
                    $toiletdetails->ORGNAME=$request->get('toilet_organisation');
                if($request->toilet_ownership!=null)
                    $toiletdetails->OWNERSHIP=$request->get('toilet_ownership');
                $toiletdetails->ACTIVE=0;
                if($request->time_open !=null)
                    $toiletdetails->time_open=$request->get('time_open');
                if($request->time_close !=null)
                {
                    $toiletdetails->time_close=$request->get('time_close');
                    Log::info("datat ".$request->get('time_close'));
                }
                if($request->free !=null)
                    $toiletdetails->free=$request->get('free');
                if($request->dr_water !=null)
                    $toiletdetails->dr_water=$request->get('dr_water');
                if($request->men !=null)
                    $toiletdetails->men=$request->get('men');
                if($request->women !=null)
                    $toiletdetails->women=$request->get('women');
                if($request->disabled !=null)
                    $toiletdetails->disabled=$request->get('disabled');
                if($request->western !=null)
                    $toiletdetails->western=$request->get('western');
                if($request->indian !=null)
                    $toiletdetails->indian=$request->get('indian');
                if($request->napkin !=null)
                    $toiletdetails->napkin=$request->get('napkin');
                if($request->clean != null)
                    $toiletdetails->cleanliness = $request->get('clean');
                if($request->maintenance != null)
                    $toiletdetails->maintenance = $request->get('maintenance');
                if($request->ambience != null)
                    $toiletdetails->ambience = $request->get('ambience');
                if($request->safety != null)
                    $toiletdetails->safety = $request->get('safety');
                if($request->water != null)
                    $toiletdetails->water = $request->get('water');
                $token = str_random(50);
                $toiletdetails->token = $token;
                $toiletdetails->save();
                $this->toilet_id = $toiletdetails->id; //keeping it -1 for new toilet
                

                $toiletdetails->OBJECTID = $toiletdetails->id;
                $toiletdetails->save();


                $toiletFeedback=new ToiletFeedback();
                $toiletFeedback->user_id=$userdetail->id;
                $toiletFeedback->toilet_id=$toiletdetails->id;
                $toiletFeedback->cleanliness=$toiletdetails->cleanliness;
                $toiletFeedback->maintenance=$toiletdetails->maintenance;
                $toiletFeedback->ambience=$toiletdetails->ambience;
                $toiletFeedback->safety=$toiletdetails->safety;
                $toiletFeedback->water= $toiletdetails->water;
                if($request->toilet_comment!=null)
                    $toiletFeedback->comment=$request->toilet_comment;
                $toiletFeedback->save();

               
                if($toiletFeedback->save())
                {
                     Mail::queue('email.activate', [ 'token' => $token, 'active' => '1', 'toilet' => $toiletdetails, 'user' => $userdetail], function($message)
                    {
                        
                        $message
                        ->to('rathin.iitb@gmail.com')
                        ->cc('tusharshahsp@gmail.com')
                        ->subject('New Toilet Added: FindaLoo')
                        ->from('findaloo.eyantra@gmail.com', 'e-Yantra IITB');
                    });

                } 
                    //add provision for sending issue to admin
            });//transaction ends here

                
            if(is_null($exception)){
                $data=array("status"=>"success","data"=>$this->toilet_id, "message"=>"Toilet Added");
            }

        }
        catch(Exception $e){
            $data=array("status"=>"fail","data"=>null, "message"=>"Something went wrong adding new toilet, please try again");
        }

     // return redirect('register')->with('success','Thank you for registering! A confirmation email has been sent to each team member. Each team member should click on the Activation link to activate their account. Follow the validation instructions as specified in confirmation e-mail to complete the registration process.');
        $data=array("status"=>"success","data"=>$this->toilet_id, "message"=>"Thank you for registering the new Toilet! It will be visible on the map after verification");
        return json_encode($data);
    }//end of create

    /**
     * create new toilet entry
     *
     * @return \Illuminate\Http\Response
     */
        // public function requestReg(Request $request){

        //     Log::info("datat ".print_r($request->all(), true));

        //     $validator = Validator::make($request->all(), [
        //         'toilet_lat' => 'required|min:5',
        //         'toilet_lng' => 'required|min:5',
        //         'toilet_name' => 'required',
        //         'g_user_id'   => 'required',
        //         'time_open'   => 'required',
        //         'time_close'   => 'required',
        //         'free'   => 'required',
        //         'dr_water'   => 'required',
        //         'men'   => 'required',
        //         'women'   => 'required',
        //         'disabled'   => 'required',
        //         'western'   => 'required',
        //         'indian'   => 'required',
        //         'napkin'   => 'required',
        //         'clean'  => 'required',
        //         'maintenance'  => 'required',
        //         'ambience'  => 'required',
        //         'safety'  => 'required',
        //         'water'  => 'required'
        //        // 'toilet_address' => 'required|digits:10|unique:User_Register,contact',
        //        // 'toilet_organisation' => 'required|min:5'
        //     ]);
        //     $toilet_id = -1;

        //     if ($validator->fails()) {
        //         $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"Toilet Registration failed");
        //         return json_encode($data);
        //     }

        //     $user = Logins::where('g_user_id',$request->g_user_id)->first();//finding the user from the token
        //     Log::info("data ".print_r($user,true));
        //     $userid=UserRegister::where(['email'=>$user->username])->pluck('id');//getting user_id
        //     Log::info("request data 1".print_r($request->all(),true));
        //     // $user = JWTAuth::parseToken()->authenticate();//finding the user from the token
        //     // $userRole=$user->role;//getting user role
        //     // if($userRole!=1 && $userRole!=2)
        //     // {
        //     //     $data=array("status"=>"fail","data"=>null, "message"=>"Only Admin or Moderator can register new toilet place");
        //     //     return json_encode($data);
        //     // }
            
        //     // $userid=UserRegister::where(['email'=>$user->username])->pluck('id');//getting user_id
        //      try{
        //         $exception=DB::transaction(function() use ($request, $userid){
                    
                    
        //             // Log::info("Request Data ".print_r($request,true));

        //         // $toiletdetails=new ToiletRegister;
        //         // $toiletdetails->lat=$request->toilet_lat;
        //         // $toiletdetails->lng=$request->toilet_lng;
        //         // $toiletdetails->user_id=$userid;
        //         // if($request->toilet_ward!=null)
        //         //     $toiletdetails->ward=$request->toilet_ward;
        //         // if($request->toilet_address!=null)
        //         //     $toiletdetails->address=$request->toilet_address;
        //         // if($request->toilet_organisation!=null)
        //         //     $toiletdetails->organisation=$request->toilet_organisation;
        //         // if($request->toilet_ownership!=null)
        //         //     $toiletdetails->ownership=$request->toilet_ownership;
        //         // $toiletdetails->save();
        //             Log::info("request data inside controller ".print_r($request->all(), true));
        //             $toiletdetails=new MSDPToiletRegister;
        //             $toiletdetails->lat=$request->get('toilet_lat');
        //             $toiletdetails->lng=$request->get('toilet_lng');
        //             $toiletdetails->user_id=$userid;
        //             if($request->toilet_name!=null)
        //                 $toiletdetails->NAME=$request->get('toilet_name');
        //             if($request->toilet_address!=null)
        //                 $toiletdetails->ADDRESS=$request->get('toilet_address');
        //             if($request->toilet_organisation!=null)
        //                 $toiletdetails->ORGNAME=$request->get('toilet_organisation');
        //             if($request->toilet_ownership!=null)
        //                 $toiletdetails->OWNERSHIP=$request->get('toilet_ownership');
        //             $toiletdetails->ACTIVE=1;
        //             if($request->time_open !=null)
        //                 $toiletdetails->time_open=$request->get('time_open');
        //             if($request->time_close !=null)
        //             {
        //                 $toiletdetails->time_close=$request->get('time_close');
        //                 Log::info("datat ".$request->get('time_close'));
        //             }
        //             if($request->free !=null)
        //                 $toiletdetails->free=$request->get('free');
        //             if($request->dr_water !=null)
        //                 $toiletdetails->dr_water=$request->get('dr_water');
        //             if($request->men !=null)
        //                 $toiletdetails->men=$request->get('men');
        //             if($request->women !=null)
        //                 $toiletdetails->women=$request->get('women');
        //             if($request->disabled !=null)
        //                 $toiletdetails->disabled=$request->get('disabled');
        //             if($request->western !=null)
        //                 $toiletdetails->western=$request->get('western');
        //             if($request->indian !=null)
        //                 $toiletdetails->indian=$request->get('indian');
        //             if($request->napkin !=null)
        //                 $toiletdetails->napkin=$request->get('napkin');
        //             if($request->clean != null)
        //                 $toiletdetails->cleanliness = $request->get('clean');
        //             if($request->maintenance != null)
        //                 $toiletdetails->maintenance = $request->get('maintenance');
        //             if($request->ambience != null)
        //                 $toiletdetails->ambience = $request->get('ambience');
        //             if($request->safety != null)
        //                 $toiletdetails->safety = $request->get('safety');
        //             if($request->water != null)
        //                 $toiletdetails->water = $request->get('water');

        //             $toiletdetails->save();
        //             $this->toilet_id = $toiletdetails->id; //keeping it -1 for new toilet
        //             $toiletdetails->OBJECTID = $toiletdetails->id;
        //              $toiletdetails->save();
        //                 //add provision for sending issue to admin
        //             });//transaction ends here

                    
        //             if(is_null($exception)){
        //                 $data=array("status"=>"success","data"=>$this->toilet_id, "message"=>"Toilet Added");
        //             }

        //     }
        //     catch(Exception $e){
        //         $data=array("status"=>"fail","data"=>null, "message"=>"Something went wrong adding new toilet, please try again");
        //     }

        //  // return redirect('register')->with('success','Thank you for registering! A confirmation email has been sent to each team member. Each team member should click on the Activation link to activate their account. Follow the validation instructions as specified in confirmation e-mail to complete the registration process.');
        //     $data=array("status"=>"success","data"=>$this->toilet_id, "message"=>"Thank you for registering the new Toilet! It will be visible on the map after verification");
        //     return json_encode($data);
        // }//end of create
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
        Log::info("data ".print_r($toiletdetails, true));
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
        log::info("radius :".$request->rad);
        $rad=0;
        $count=0;
        if($request->rad==null)
            $rad=1.5;//keeping default distance as 1.5 km
        else
            $rad=$request->rad;
        if($request->count==null)
            $count=20;//keeping default distance as 1.5 km
        else
            $count=$request->count;
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
        $toiletdetails=DB::select('SELECT OBJECTID, NAME,lat,lng, MSDPUSERToilet_Block.CONDITION, CONDITION_RAW, ACTIVE, address, ( 6371 * acos( cos( radians(:user_lat1) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(:user_lng) ) + sin( radians(:user_lat2) ) * sin( radians( lat ) ) ) ) AS distance FROM MSDPUSERToilet_Block WHERE ACTIVE = 1 HAVING distance < :rad order by distance asc limit :count',['user_lat1'=>$user_lat,'user_lat2'=>$user_lat,'user_lng'=>$user_lng,'rad'=>$rad, 'count'=>$count]) ;
        log::info("number of toilets ".print_r($toiletdetails,true));
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
        
        $toiletdetails=ToiletRegister::where(['id'=>$request->toilet_id])->select('OBJECTID', 'NAME','lat','lng','wardheader','address','ORGNAME','ownership','countms','countfs','condition','condition_raw','cleanliness','maintenance' ,'ambience','water','safety','SURVEYEDDA')->get();
        Log::info("data ".print_r($toiletdetails,true));
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
     * Display the SOS toilet.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function showSOS(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_lat' => 'required|min:5',
            'user_lng' => 'required|min:5'
        ]);
        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"Location coordinates missing");
            return json_encode($data);
        }
        $rad=0;
        if($request->rad==null)
            $rad=20;//keeping default distance as 1.5 km
        else
            $rad=$request->rad;
        $user_lat=$request->user_lat;
        $user_lng=$request->user_lng;
        $toiletdetails=DB::select('SELECT OBJECTID,lat,lng,address, ( 6371 * acos( cos( radians(:user_lat1) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(:user_lng) ) + sin( radians(:user_lat2) ) * sin( radians( lat ) ) ) ) AS distance FROM MSDPUSERToilet_Block WHERE ACTIVE =1 HAVING distance < :rad order by distance asc limit 1',['user_lat1'=>$user_lat,'user_lat2'=>$user_lat,'user_lng'=>$user_lng,'rad'=>$rad]);
        if(sizeof($toiletdetails)>0)
        {
            $toiletdetails=$toiletdetails[0];
            $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilets fetched");
            return json_encode($data);
        }
        $data=array("status"=>"fail","data"=>null, "message"=>"No closest toilet found in your area in the radius of ".$rad." kms");
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
        
        $toiletdetails=MSDPToiletRegister::where(['id'=>$request->toilet_id])->get();
        Log::info("data ".print_r($toiletdetails,true));
        if(sizeof($toiletdetails)>0)
        {
            $toiletdetails=$toiletdetails[0];
            $toiletphoto=ToiletImages::where('toilet_id',$request->toilet_id)->select('user_id','image_name','image_title','created_at')->get();
           //$toiletphoto=$toiletphoto[0];
            $ratingsCount=ToiletFeedback::where('toilet_id',$request->toilet_id)->count();
            $toiletdetails->toilet_image=$toiletphoto;
            $toiletdetails->ratingsCount=$ratingsCount;
            // Log::info("data about toiletdetails".print_r($toiletdetails,true));
            $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilet details fetched");
        }
        else
            $data=array("status"=>"fail","data"=>null, "message"=>"invalid toilet id");
        return json_encode($data);
    }
    /**
     * API for creating visiting history for user for each toilet
     *
     * @param  int  Request $request
     * @return \Illuminate\Http\Response
     */
    public function addHistory(Request $request)//for registered user
    {
        $validator = Validator::make($request->all(), [
            'toilet_id' => 'required'
            //'toilet_cleanliness' => 'required|numeric|min:1|max:5',
            //'toilet_maintenance' => 'required',
            //'toilet_ambience' => 'required'

        ]);
        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"No toilet id passed");
            return json_encode($data);
        }
        $user = JWTAuth::parseToken()->authenticate();//finding the user from the token
        $userid=UserRegister::where(['email'=>$user->username])->pluck('id');//getting user_id

        //$data=array("status"=>"fail","data"=>null, "message"=>"Something went wrong in storing history");
        try{
            $exception=DB::transaction(function() use ($request, $userid){
                $toiletvisit=new ToiletVisits;
                $toiletvisit->user_id=$userid;
                $toiletvisit->toilet_id=$request->toilet_id;
                // if($request->toilet_comment!=null)
                    // $toiletFeedback->comment=$request->toilet_comment;
                $toiletvisit->save();
            });//transaction ends here
            if(is_null($exception)){
                $data=array("status"=>"success","data"=>null, "message"=>"Visit entry added");
            }

        }
        catch(Exception $e){
            $data=array("status"=>"fail","data"=>null, "message"=>"Something went wrong in storing history");
        }
        $data=array("status"=>"success","data"=>null, "message"=>"Visit history added");
        return json_encode($data);
    }

      /**
     * for fetching all the toilets visited by the user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showVisitHistory()
    {
        $user = JWTAuth::parseToken()->authenticate();//finding the user from the token
        $userid=UserRegister::where(['email'=>$user->username])->pluck('id');//getting user_id
        // $visits = ToiletVisits::where(['user_id'=>$userid])->get();
        $visits = DB::table('Toilet_Visits')
        ->join('MSDPUSERToilet_Block','Toilet_Visits.toilet_id','=','MSDPUSERToilet_Block.OBJECTID')
        ->select('Toilet_Visits.id','Toilet_Visits.user_id','MSDPUSERToilet_Block.OBJECTID','MSDPUSERToilet_Block.NAME','MSDPUSERToilet_Block.ADDRESS','Toilet_Visits.created_at')
        ->where(['user_id'=>$userid])
        ->get();
        if(sizeof($visits)>0){

            $data=array("status"=>"success","data"=>$visits, "message"=>"Visits fetched");
        }
        else
            $data=array("status"=>"fail","data"=>null, "message"=>"No visit history found");
        return json_encode($data);
    }

    /*
     * API for reporting issues for each toilet
     *
     * @param  int  Request $request
     * @return \Illuminate\Http\Response
     */
    public function reportIssue(Request $request)//for registered user
    {

        $validator = Validator::make($request->all(), [
            'toilet_id' => 'required',
            'g_user_id' => 'required',
            'CLEANLINESS' => 'required_without_all:CLEANLINESS,MECHANICAL,ELECTRICAL,PLUMBING,EMPTYING,OTHER,COMMENT|numeric|min:0|max:1',
            'MECHANICAL' => 'required_without_all:CLEANLINESS,MECHANICAL,ELECTRICAL,PLUMBING,EMPTYING,OTHER,COMMENT|numeric|min:0|max:1',
            'ELECTRICAL' => 'required_without_all:CLEANLINESS,MECHANICAL,ELECTRICAL,PLUMBING,EMPTYING,OTHER,COMMENT|numeric|min:0|max:1',
            'PLUMBING' => 'required_without_all:CLEANLINESS,MECHANICAL,ELECTRICAL,PLUMBING,EMPTYING,OTHER,COMMENT|numeric|min:0|max:1',
            'EMPTYING' => 'required_without_all:CLEANLINESS,MECHANICAL,ELECTRICAL,PLUMBING,EMPTYING,OTHER,COMMENT|numeric|min:0|max:1',
            'OTHER' => 'required_without_all:CLEANLINESS,MECHANICAL,ELECTRICAL,PLUMBING,EMPTYING,OTHER,COMMENTCLEANLINESS,MECHANICAL,ELECTRICAL,PLUMBING,EMPTYING,OTHER,COMMENT|numeric|min:0|max:1',

            'COMMENT' => 'required_without_all:CHOKING,MECHANICAL,ELECTRICAL,PLUMBING,SEWAGE,CLEANLINESS'
            // 'toiletPht' => 'image:jpeg,bmp,png|max:3000'
            ],
            [
            'required_without_all' => 'Please select atleast one option',
            // 'toiletPht.image' => 'Please upload only .jpg/.png/.bmp file.',
            // 'toiletPht.max' => 'Size of the file should be less than 3MB.'
        ]);
        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"Incomplete data");
            return json_encode($data);
        }


        $user = Logins::where('g_user_id',$request->g_user_id)->first();//finding the user from the token
        Log::info("data ".print_r($user,true));
        $userid=UserRegister::where(['email'=>$user->username])->pluck('id');//getting user_id
        //$userid=6;
        

        $checkUser=ToiletFeedback::where(['toilet_id'=>$request->toilet_id, 'user_id'=>$userid])->count();
        // $user = JWTAuth::parseToken()->authenticate();//finding the username from the token
        // $userid=UserRegister::where(['email'=>$user->username])->pluck('id');//getting user_id

        //$data=array("status"=>"fail","data"=>null, "message"=>"Something went wrong in storing history");

        try{
            $exception=DB::transaction(function() use ($request, $userid){
                $issue=new ReportIssues;
                $issue->user_id=$userid;
                $issue->toilet_id=$request->toilet_id;
                // $issue->active=$request->toilet_id;
                if($request->COMMENT!=null)
                    $issue->COMMENT=$request->COMMENT;
                if($request->CLEANLINESS!=null)
                    $issue->CLEANLINESS=$request->CLEANLINESS;
                // if($request->CHOKING!=null)
                //     $issue->CHOKING=$request->CHOKING;
                if($request->MECHANICAL!=null)
                    $issue->MECHANICAL=$request->MECHANICAL;
                if($request->ELECTRICAL!=null)
                    $issue->ELECTRICAL=$request->ELECTRICAL;
                if($request->PLUMBING!=null)
                    $issue->PLUMBING=$request->PLUMBING;
                if($request->EMPTYING!=null)
                    $issue->EMPTYING=$request->EMPTYING;
                $issue->save();

                //add provision for sending issue to admin
            });//transaction ends here
            if(is_null($exception)){
                $data=array("status"=>"success","data"=>null, "message"=>"Issue Reported");
            }

        }
        catch(Exception $e){
            $data=array("status"=>"fail","data"=>null, "message"=>"Something went wrong in reporting the issue, please try again");
        }
        $data=array("status"=>"success","data"=>null, "message"=>"Issue Reported");
        return json_encode($data);
    }

    // public function reportToiletImage(Request $request)
    // {


    //     $validator = Validator::make($request->all(), [
    //         'toilet_id' => 'required',
    //         'g_user_id' => 'required',
           
    //         'toiletPht' => 'image:jpeg,bmp,png|max:3000'
    //         ],
    //         [
    //         'required_without_all' => 'Please select atleast one option',
    //         'toiletPht.image' => 'Please upload only .jpg/.png/.bmp file.',
    //         'toiletPht.max' => 'Size of the file should be less than 3MB.'
    //     ]);
    //     if ($validator->fails()) {
    //         $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"Incomplete data");
    //         return json_encode($data);
    //     }


    //     $user = Logins::where('g_user_id',$request->g_user_id)->first();//finding the user from the token
    //     Log::info("data ".print_r($user,true));
    //     $userid=UserRegister::where(['email'=>$user->username])->pluck('id');//getting user_id

    //     $checkUser=ToiletFeedback::where(['toilet_id'=>$request->toilet_id, 'user_id'=>$userid])->count();

    //     try{
    //         $exception=DB::transaction(function($request) use ($request, $userid){
    //             $issue=new ReportIssues;
    //             $issue->user_id=$userid;
    //             $issue->toilet_id=$request->toilet_id;
    //             // $issue->active=$request->toilet_id;
    //             if($request->COMMENT!=null)
    //                 $issue->COMMENT=$request->COMMENT;
    //             if($request->CLEANLINESS!=null)
    //                 $issue->CLEANLINESS=$request->CLEANLINESS;
    //             // if($request->CHOKING!=null)
    //             //     $issue->CHOKING=$request->CHOKING;
    //             if($request->MECHANICAL!=null)
    //                 $issue->MECHANICAL=$request->MECHANICAL;
    //             if($request->ELECTRICAL!=null)
    //                 $issue->ELECTRICAL=$request->ELECTRICAL;
    //             if($request->PLUMBING!=null)
    //                 $issue->PLUMBING=$request->PLUMBING;
    //             if($request->EMPTYING!=null)
    //                 $issue->EMPTYING=$request->EMPTYING;
    //             $issue->save();

    //             //add provision for sending issue to admin
    //         });//transaction ends here
    //         if(is_null($exception)){
    //             $data=array("status"=>"success","data"=>null, "message"=>"Issue Reported");
    //         }

    //     }
    //     catch(Exception $e){
    //         $data=array("status"=>"fail","data"=>null, "message"=>"Something went wrong in reporting the issue, please try again");
    //     }
    //     $data=array("status"=>"success","data"=>null, "message"=>"Issue Reported");
    //     return json_encode($data);
    //     if($request->toiletPht!=null){
    //                 //do image uploading here
    //                 $path = public_path().'/img/toilets/';
    //                 $ext = strtolower($request->file('toiletPht')->getClientOriginalExtension());
    //                 $filename = str_random(15).".".$ext;
    //                 $request->file('toiletPht')->move($path, $filename);
    //                 $toiletPhoto=new ToiletImages;
    //                 $toiletPhoto->user_id=$userid;
    //                 $toiletPhoto->toilet_id=$request->toilet_id;
    //                 $toiletPhoto->image_name=$filename;
    //                 $toiletPhoto->active=1;
    //                 $toiletPhoto->save();
    //                 $issue->image_id=$toiletPhoto->id;
    //             }
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        //

        $validator = Validator::make($request->all(), [
            'toilet_id' => 'required'
        ]);
        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"Please Provide one Toilet");
            return json_encode($data);
        }
        
        $toiletdetails=MSDPToiletRegister::where(['OBJECTID'=>$request->toilet_id])->first();

        Log::info("Request data ".print_r($request->all(),true));
        $toilet_count=MSDPToiletRegister::where(['OBJECTID'=>$request->toilet_id])->count();
        Log::info("inside_editfacility count ".$toilet_count." toiletdetails : ".print_r($toiletdetails, true));
        if($toilet_count >0)
        {

            $toiletdetails->free = $request->get('free');
            $toiletdetails->men = $request->get('men');
            $toiletdetails->women = $request->get('women');
            $toiletdetails->disabled = $request->get('disabled');
            $toiletdetails->western = $request->get('western');
            $toiletdetails->indian = $request->get('indian');
            $toiletdetails->napkin = $request->get('napkin');
            $toiletdetails->dr_water = $request->get('dr_water');
            
            $toiletdetails->save();
            // Log::info("data about toiletdetails".print_r($toiletdetails,true));
            $data=array("status"=>"success","data"=>0, "message"=>"Edited Toilet details added. Refresh toilet to view changes");
        }
        else
            $data=array("status"=>"fail","data"=>0, "message"=>"invalid toilet id");
        return json_encode($data);
    }


    /**
     * Show the form for editing the specified time.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editTime(Request $request)
    {
        //

        $validator = Validator::make($request->all(), [
            'toilet_id' => 'required'
        ]);
        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"Please Provide one Toilet");
            return json_encode($data);
        }
        
        $toiletdetails=MSDPToiletRegister::where(['OBJECTID'=>$request->toilet_id])->first();

        Log::info("Request data ".print_r($request->all(),true));
        $toilet_count=MSDPToiletRegister::where(['OBJECTID'=>$request->toilet_id])->count();
        Log::info("inside_editfacility count ".$toilet_count." toiletdetails : ".print_r($toiletdetails, true));
        if($toilet_count >0)
        {

            $toiletdetails->time_open = $request->get('time_open');
            $toiletdetails->time_close = $request->get('time_close');            
            $toiletdetails->save();
            // Log::info("data about toiletdetails".print_r($toiletdetails,true));
            $data=array("status"=>"success","data"=>0, "message"=>"Request Toilet Timings updated. Refresh toilet to view changes");
        }
        else
            $data=array("status"=>"fail","data"=>0, "message"=>"invalid toilet id");
        return json_encode($data);
    }
    /**
     * API for entering/updating feedback for specific toilet
     *
     * @param  int  Request $request
     * @return \Illuminate\Http\Response
     */
    public function oldaddFeedback(Request $request)
    {
        Log::info("feedback");

        $validator = Validator::make($request->all(), [
            'toilet_id' => 'required',
            'toilet_cleanliness' => 'required|numeric|min:1|max:5',
            'toilet_maintenance' => 'required|numeric|min:1|max:5',
            'toilet_ambience' => 'required|numeric|min:1|max:5'
        ]);

        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"incomplete feedback data");
            return json_encode($data);
        }
        $user = JWTAuth::parseToken()->authenticate();//finding the user from the token
        $userid=UserRegister::where(['email'=>$user->username])->pluck('id');//getting user_id
        //$userid=6;
        $ratings=MSDPToiletRegister::where('OBJECTID',$request->toilet_id)->first();
        if(sizeof($ratings)==0){
            $data=array("status"=>"fail","data"=>null, "message"=>"Unknown Toilet id passed");
            return json_encode($data);
        }
        $checkUser=ToiletFeedback::where(['toilet_id'=>$request->toilet_id, 'user_id'=>$userid])->count();
        // $toiletdetails=ToiletFeedback::where('toilet_id',$request->toilet_id)->count();
            // return $toiletdetails;
        
        DB::transaction(function() use ($request, $userid, $checkUser, $ratings){
        // $user = JWTAuth::parseToken()->authenticate();//finding the user from the token
        // $userid=UserRegister::where(['email'=>$user->username])->pluck('id');//getting user_id
            
            if($checkUser==0){//for the first time
                $toiletFeedback=new ToiletFeedback;
                $toiletFeedback->user_id=$userid;
                $toiletFeedback->toilet_id=$request->toilet_id;
                $toiletFeedback->cleanliness=$request->toilet_cleanliness;
                $toiletFeedback->maintenance=$request->toilet_maintenance;
                $toiletFeedback->ambience=$request->toilet_ambience;
                if($request->toilet_comment!=null)
                    $toiletFeedback->comment=$request->toilet_comment;
                $toiletFeedback->save();
                $feedbackCount=ToiletFeedback::where('toilet_id',$request->toilet_id)->count();
                //$ratings=MSDPToiletRegister::where('OBJECTID',$request->toilet_id)->first();
                $ratings->CLEANLINESS=($ratings->CLEANLINESS*$feedbackCount+$request->toilet_cleanliness)/($feedbackCount+1);
                $ratings->MAINTENANCE=($ratings->MAINTENANCE*$feedbackCount+$request->toilet_maintenance)/($feedbackCount+1);
                $ratings->AMBIENCE=($ratings->AMBIENCE*$feedbackCount+$request->toilet_ambience)/($feedbackCount+1);
                //}
                $ratings->CONDITION_RAW=($ratings->CLEANLINESS+$ratings->MAINTENANCE+$ratings->AMBIENCE)/3;//overall rating is average all three subratings
            }
            else{//rediting the old feedback
                $toiletFeedback=ToiletFeedback::where(['toilet_id'=>$request->toilet_id, 'user_id'=>$userid])->first();//get previous rating from the user

                $feedbackCount=ToiletFeedback::where('toilet_id',$request->toilet_id)->count()+1;//finding the count of feedbacks
                $ratings->CLEANLINESS=($ratings->CLEANLINESS*$feedbackCount-$toiletFeedback->cleanliness+$request->toilet_cleanliness)/($feedbackCount);//readjusting the average
                $ratings->MAINTENANCE=($ratings->MAINTENANCE*$feedbackCount-$toiletFeedback->maintenance+$request->toilet_maintenance)/($feedbackCount);
                $ratings->AMBIENCE=($ratings->AMBIENCE*$feedbackCount-$toiletFeedback->ambience+$request->toilet_ambience)/($feedbackCount);
                $ratings->CONDITION_RAW=($ratings->CLEANLINESS+$ratings->MAINTENANCE+$ratings->AMBIENCE)/3;//overall rating is 
                
                $toiletFeedback->cleanliness=$request->toilet_cleanliness;
                $toiletFeedback->maintenance=$request->toilet_maintenance;
                $toiletFeedback->ambience=$request->toilet_ambience;
                if($request->toilet_comment!=null)
                    $toiletFeedback->comment=$request->toilet_comment;
                $toiletFeedback->save();

            }
            //$toilet_id=$request->toilet_id;
            //return $toiletdetails;
            //$toiletdetails=DB::select('SELECT AVG(cleanliness) c, AVG(maintenance) m, AVG(ambience) a FROM toilet_feedback where toilet_id=:toilet_id',['toilet_id'=>$request->toilet_id]);
            //$toiletdetails=$toiletdetails[0];
            //updating the rating of the toilet
            //if no feedback before, multiply old rating with one add new and divde it by two
            //if previous feedbacks are their, find their counts, multiply it with old rating, add new, diidfe by count+1
            // if($feedbackCount==1){
            //     $ratings->cleanliness=($ratings->cleanliness+$request->toilet_cleanliness)/2;
            //     $ratings->maintenance=($ratings->maintenance+$request->toilet_maintenance)/2;
            //     $ratings->ambience=($ratings->ambience+$request->toilet_ambience)/2;
            // }
            // else{
            //Log::info($ratings);
            //}
            //average all three subratings
            if($ratings->CONDITION_RAW<=2.33 && $ratings->CONDITION_RAW>=1)
                $ratings->CONDITION='Poor';
            else if($ratings->CONDITION_RAW>2.33 && $ratings->CONDITION_RAW<=3.66)
                $ratings->CONDITION='Moderate';
            else
                $ratings->CONDITION='Good';
            $ratings->save();
        });//transaction ends here
        $data=array("status"=>"success","data"=>null, "message"=>"Thank you for your Feedback");
        return json_encode($data);
        // $data=array("status"=>"fail","data"=>null, "message"=>"Multiple feedbacks not allowed");
        // return json_encode($data);
    }
    /**
     * API for entering/updating feedback for specific toilet
     *
     * @param  int  Request $request
     * @return \Illuminate\Http\Response
     */
    public function addFeedback(Request $request)
    {
        Log::info("feedback");

        $validator = Validator::make($request->all(), [
            'toilet_id' => 'required',
            'toilet_cleanliness' => 'required|numeric|min:0|max:5',
            'toilet_maintenance' => 'required|numeric|min:0|max:5',
            'toilet_ambience' => 'required|numeric|min:0|max:5',
            'toilet_safety' => 'required|numeric|min:0|max:5',
            'toilet_water' => 'required|numeric|min:0|max:5',
            'g_user_id' => 'required|min:4',
        ]);

        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"incomplete feedback data");
            return json_encode($data);
        }
        $user = Logins::where('g_user_id',$request->g_user_id)->first();//finding the user from the token
        Log::info("data ".print_r($user,true));
        $userid=UserRegister::where(['email'=>$user->username])->pluck('id');//getting user_id
        //$userid=6;
        $ratings=MSDPToiletRegister::where('OBJECTID',$request->toilet_id)->first();
        $ratings_count=MSDPToiletRegister::where('OBJECTID',$request->toilet_id)->count();
        if($ratings_count==0){
            $data=array("status"=>"fail","data"=>null, "message"=>"Unknown Toilet id passed");
            return json_encode($data);
        }
        $checkUser=ToiletFeedback::where(['toilet_id'=>$request->toilet_id, 'user_id'=>$userid])->count();
        // $toiletdetails=ToiletFeedback::where('toilet_id',$request->toilet_id)->count();
            // return $toiletdetails;
        
        DB::transaction(function() use ($request, $userid, $checkUser, $ratings){
        // $user = JWTAuth::parseToken()->authenticate();//finding the user from the token
        // $userid=UserRegister::where(['email'=>$user->username])->pluck('id');//getting user_id
            
            if($checkUser==0){//for the first time
                $toiletFeedback=new ToiletFeedback;
                $toiletFeedback->user_id=$userid;
                $toiletFeedback->toilet_id=$request->toilet_id;
                $toiletFeedback->cleanliness=$request->toilet_cleanliness;
                $toiletFeedback->maintenance=$request->toilet_maintenance;
                $toiletFeedback->ambience=$request->toilet_ambience;
                $toiletFeedback->safety=$request->toilet_safety;
                $toiletFeedback->water= $request->toilet_water;
                if($request->toilet_comment!=null)
                    $toiletFeedback->comment=$request->toilet_comment;
                $toiletFeedback->save();
                $feedbackCount=ToiletFeedback::where('toilet_id',$request->toilet_id)->count();
                //$ratings=MSDPToiletRegister::where('OBJECTID',$request->toilet_id)->first();
                $ratings->CLEANLINESS=($ratings->CLEANLINESS*$feedbackCount+$request->toilet_cleanliness)/($feedbackCount+1);
                $ratings->MAINTENANCE=($ratings->MAINTENANCE*$feedbackCount+$request->toilet_maintenance)/($feedbackCount+1);
                $ratings->AMBIENCE=($ratings->AMBIENCE*$feedbackCount+$request->toilet_ambience)/($feedbackCount+1);
                $ratings->SAFETY=($ratings->SAFETY*$feedbackCount+$request->toilet_safety)/($feedbackCount+1);
                $ratings->WATER=($ratings->WATER*$feedbackCount+$request->toilet_water)/($feedbackCount+1);
                //}
                $ratings->CONDITION_RAW=($ratings->CLEANLINESS+$ratings->MAINTENANCE+$ratings->AMBIENCE+$ratings->SAFETY + $ratings->WATER)/5;//overall rating is average all three subratings
            }
            else{//rediting the old feedback
                $toiletFeedback=ToiletFeedback::where(['toilet_id'=>$request->toilet_id, 'user_id'=>$userid])->first();//get previous rating from the user

                $feedbackCount=ToiletFeedback::where('toilet_id',$request->toilet_id)->count()+1;//finding the count of feedbacks
                $ratings->CLEANLINESS=($ratings->CLEANLINESS*$feedbackCount-$toiletFeedback->cleanliness+$request->toilet_cleanliness)/($feedbackCount);//readjusting the average
                $ratings->MAINTENANCE=($ratings->MAINTENANCE*$feedbackCount-$toiletFeedback->maintenance+$request->toilet_maintenance)/($feedbackCount);
                $ratings->AMBIENCE=($ratings->AMBIENCE*$feedbackCount-$toiletFeedback->ambience+$request->toilet_ambience)/($feedbackCount);
                $ratings->SAFETY=($ratings->SAFETY*$feedbackCount-$toiletFeedback->safety+$request->toilet_safety)/($feedbackCount);
                $ratings->WATER=($ratings->WATER*$feedbackCount-$toiletFeedback->water+$request->toilet_water)/($feedbackCount);
                $ratings->CONDITION_RAW=($ratings->CLEANLINESS+$ratings->MAINTENANCE+$ratings->AMBIENCE+$ratings->SAFETY+$ratings->WATER)/5;//overall rating is 
                
                $toiletFeedback->cleanliness=$request->toilet_cleanliness;
                $toiletFeedback->maintenance=$request->toilet_maintenance;
                $toiletFeedback->ambience=$request->toilet_ambience;
                $toiletFeedback->safety= $request->toilet_safety;
                $toiletFeedback->water= $request->toilet_water;
                if($request->toilet_comment!=null)
                    $toiletFeedback->comment=$request->toilet_comment;
                $toiletFeedback->save();

            }
            
            if($ratings->CONDITION_RAW<=2.33 && $ratings->CONDITION_RAW>=0)
                $ratings->CONDITION='Poor';
            else if($ratings->CONDITION_RAW>2.33 && $ratings->CONDITION_RAW<=3.66)
                $ratings->CONDITION='Moderate';
            else  if($ratings->CONDITION_RAW>3.66)
                $ratings->CONDITION='Good';
            $ratings->save();
        });//transaction ends here

        Log::info("data the way you want");
        $data=array("status"=>"success","data"=>null, "message"=>"Thank you for your Feedback");
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
        Log::info("data ".print_r($request->all(),true));
        $validator = Validator::make($request->all(), [
            'toiletPht' => 'required|image:jpg,jpeg,bmp,png',
            'toilet_id' => 'required',
            'g_user_id' => 'required',
            // max:3000',
            ],

            [
            'toiletPht.required' => 'Please select a photo.',
            'toiletPht.image' => 'Please upload only .jpg/.png/.bmp file.',
            'toilet_id' => 'Size of the file should be less than 3MB.',
            'g_user_id' => 'The image should be of at least 800 x 600 resolution'
            // 'toiletPht.max' => 'Size of the file should be less than 3MB.',
            // 'toiletPht.dimensions' => 'The image should be of at least 800 x 600 resolution'
        ]);
        if ($validator->fails()) {
            Log::info("data reached upload fail 1");
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"Photo upload failed");
            return json_encode($data);
        }

        
        if($request->toilet_id != -1){
            $cur_toilet= MSDPToiletRegister::where('OBJECTID',$request->toilet_id)->count();
            if($cur_toilet==0 || is_null($cur_toilet)){
                Log::info("data reached upload fail 2");
                $data=array("status"=>"fail","data"=>null, "message"=>"Unknown Toilet id passed");
                return json_encode($data);
            }
        }


        DB::transaction(function() use ($request){
            if($request->toilet_id == -1){
                $path = public_path().'/img/toilets/requested_toilets/';
            }else{
                $path = public_path().'/img/toilets/'.$request->toilet_id.'/';
            }
            $user = Logins::where('g_user_id',$request->g_user_id)->first();//finding the user from the token
            // Log::info("data ".print_r($user,true));
            $userid=UserRegister::where(['email'=>$user->username])->pluck('id');//getting user_id
            // $user = JWTAuth::parseToken()->authenticate();//finding the user from the token
            // $userid=UserRegister::where(['email'=>$user->username])->pluck('id');//getting user_id
            $ext = strtolower($request->file('toiletPht')->getClientOriginalExtension());
            
            $toiletPhoto=new ToiletImages;

            $toiletPhoto->user_id=$userid;
            $toiletPhoto->toilet_id=$request->toilet_id;
            $toiletPhoto->save();
            $filename = str_random(15). $toiletPhoto->getKey().".".$ext;

            $toiletPhoto->image_name=$filename;
            if($request->name!=null)
                $toiletPhoto->image_title=$request->name;
            // if($cur_toilet->ACTIVE == 0){
            //     $toiletPhoto->active=0;
            // }
            $toiletPhoto->save();
            
            $request->file('toiletPht')->move($path, $filename);
        });
        $data=array("status"=>"success","data"=>null, "message"=>"Image uploaded succesfully");
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

    /**
     * API for entering/updating feedback for specific toilet
     *
     * @param  int  Request $request
     * @return \Illuminate\Http\Response
     */
    public function giveGenFeedback(Request $request)
    {
        Log::info("feedback");

        $validator = Validator::make($request->all(), [
            'user_feedback' => 'required',
            'g_user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"incomplete feedback data");
            return json_encode($data);
        }

        
        $user = Logins::where('g_user_id',$request->g_user_id)->first();//finding the user from the token
        Log::info("data ".print_r($user,true));
        
        $user_count = Logins::where('g_user_id',$request->g_user_id)->count();
        if ($user_count <= 0) {
            $data=array("status"=>"fail","data"=> null, "message"=>"Sorry!!! user not found");
            return json_encode($data);
        }
    
        $feedback =  new General_Feedback();

        DB::transaction(function() use ($request, $user, $feedback){
            

            $feedback->user_feedback = $request->user_feedback;
            $feedback->g_user_id = $request->g_user_id;
            if($feedback->save()){
                
                Log::info("data ".$user. "feedback".$feedback);
                Mail::queue('email.feedback_received', array('user' => $user->username,'feedback' => $feedback->user_feedback) , function($message) use($user, $feedback)
                {
                    
                    $message->from("admin@findaloo.org", "Find_a_Loo")->to('tusharshahsp@gmail.com','vikrant.ferns@gmail.com')->bcc('admin@e-yantra.org')->subject('Feedback Received');
                });
            }
            else{
                $data=array("status"=>"fail","data"=>null, "message"=>"Feedback could not be saved");
                    return json_encode($data);
            }

        });

        Log::info("data the way you want");
        $data=array("status"=>"success","data"=>null, "message"=>"Thank you for your Feedback");
        return json_encode($data);
        
    }


    /**
     * API for requesting clean for a specific toilet
     *
     * @param  int  Request $request
     * @return \Illuminate\Http\Response
     */
    public function requestClean(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'g_user_id' => 'required',
            'toilet_id' => 'required',
        ]);


        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"incomplete request data");
            return json_encode($data);
        }

        
        $user = Logins::where('g_user_id',$request->g_user_id)->first();//finding the user from the token
        Log::info("data ".print_r($user,true));
        

        $user_count = Logins::where('g_user_id',$request->g_user_id)->count();
        if ($user_count <= 0) {
            $data=array("status"=>"fail","data"=> null, "message"=>"Sorry!!! user not found");
            return json_encode($data);
        }

        // $userid=UserRegister::where(['email'=>$user->username])->pluck('id');//getting user_id
        //$userid=6;
        $cur_toilet= MSDPToiletRegister::where('OBJECTID',$request->toilet_id)->count();
        Log::info("current toilet ".print_r($cur_toilet,true));
        if($cur_toilet==0 || is_null($cur_toilet)){
            $data=array("status"=>"fail","data"=>null, "message"=>"Unknown Toilet id passed");
            return json_encode($data);
        }

    
        
        DB::transaction(function() use ($request, $user, $cur_toilet){
            
            $cur_toilet= MSDPToiletRegister::where('OBJECTID',$request->toilet_id)->first();
            Log::info("current toilet data ".print_r($cur_toilet,true));
            $cur_toilet->CLEAN_REQUEST = $cur_toilet->CLEAN_REQUEST+1;
            if(!$cur_toilet->save()){
                $data=array("status"=>"fail","data"=>null, "message"=>"Request could not be made for cleaning the toilet");
                    return json_encode($data);
            }
        });

        Log::info("success tushar");
        $data=array("status"=>"success","data"=>null, "message"=>"Thank you for making the request to clean the toilet");
        return json_encode($data);
        
    }


    public function toiletActive(Request $request)
    {
        Log::info("feedback");

        $validator = Validator::make($request->all(), [
            'toilet_id' => 'required',
            'active' => 'required',
            'g_user_id' => 'required|min:4',
        ]);

        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"incomplete data");
            return json_encode($data);
        }
        $user = Logins::where('g_user_id',$request->g_user_id)->first();//finding the user from the token
        Log::info("data ".print_r($user,true));
        $userdetail=UserRegister::where(['email'=>$user->username])->first();//getting user_id
        
        try{
            $exception =DB::transaction(function() use ($request, $userdetail){
                if($request->active!=null){

                    $token = str_random(50);
                    $toiletdetails= MSDPToiletRegister::where('OBJECTID',$request->toilet_id)->first();
                    $toiletdetails->token = $token;
                    $toiletdetails->save();
                    if($request->active == 1){
                        Mail::queue('email.activate', [ 'token' => $token, 'active' => '1', 'toilet' => $toiletdetails, 'user' => $userdetail], function($message)
                        {
                            
                            $message
                            ->to('rathin.iitb@gmail.com')
                            ->bcc('tusharshahsp@gmail.com')
                            ->subject('Toilet Add Request: FindaLoo')
                            ->from('findaloo.eyantra@gmail.com', 'e-Yantra IITB');
                        });
                    }
                    elseif ($request->active == 0) {
                        Mail::queue('email.deactivate', [ 'token' => $token, 'active' => '0', 'toilet' => $toiletdetails, 'user' => $userdetail], function($message)
                        {
                            
                            $message
                            ->to('rathin.iitb@gmail.com')
                            ->bcc('tusharshahsp@gmail.com')
                            ->subject('Toilet Remove Request: FindaLoo')
                            ->from('findaloo.eyantra@gmail.com', 'e-Yantra IITB');
                        });
                    }
                }  
            });//transaction ends here
            if(is_null($exception)){
                    $data=array("status"=>"success","data"=>null, "message"=>"Toilet will be updated shortly after verification");
            }
        }
        catch(Exception $e){
            $data=array("status"=>"fail","data"=>null, "message"=>"Something went wrong in updating the toilet status, please try again");
            DB::rollback();
        }
        $data=array("status"=>"success","data"=>null, "message"=>"Toilet will be updated shortly after verification");
        return json_encode($data);
      
    }


    public function toiletstats(Request $request)
    {
        Log::info("toiletstats");

        $validator = Validator::make($request->all(), [
            'criteria' => 'required',
        ]);



        if ($validator->fails()) 
        {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"criteria not specified");
            return json_encode($data);
        }

        if($request->criteria==1)
        {
           $toiletdetails[0]=DB::select('SELECT OBJECTID, NAME,lat,lng FROM MSDPUSERToilet_Block WHERE CONDITION_RAW <2.33') ;

           $toiletdetails[1]=DB::select('SELECT OBJECTID, NAME,lat,lng FROM MSDPUSERToilet_Block WHERE CONDITION_RAW BETWEEN 2.33 AND 3.66') ;

           $toiletdetails[2]=DB::select('SELECT OBJECTID, NAME,lat,lng FROM MSDPUSERToilet_Block WHERE CONDITION_RAW > 3.66') ;


            log::info("number of toilets ".print_r($toiletdetails,true));
            if(sizeof($toiletdetails)>0)
            {
                $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilets fetched");
                return json_encode($data);
            }
            $data=array("status"=>"success","data"=>[], "message"=>"No toilets found in your area");
            return json_encode($data); 
        }

        if($request->criteria==2)
        {
            $max_visit1=DB::select('SELECT MAX(t) as max_c From ( select  COUNT(toilet_id) as t FROM Toilet_Visits ) as max_count')  ;
            Log::info("data test" .print_r($max_visit1,true));
            $max_visit = $max_visit1[0]->max_c;
            

            if(is_null($max_visit) || empty($max_visit)){
                $max_visit = 0;
            }

            $toiletdetails[0] = ToiletVisits::select( DB::raw('COUNT(toilet_id) as toilet_visits'), 'OBJECTID', 'NAME','lat','lng')
                ->leftjoin('MSDPUSERToilet_Block as mutb','mutb.OBJECTID', '=' ,'toilet_id' )->groupBy('toilet_id')
                ->havingRaw('COUNT(toilet_id) > 0*'.$max_visit.' AND COUNT(toilet_id) <= 0.25*'.$max_visit)->get();

            $toiletdetails[1] = ToiletVisits::select( DB::raw('COUNT(toilet_id) as toilet_visits'), 'OBJECTID', 'NAME','lat','lng')
                ->leftjoin('MSDPUSERToilet_Block as mutb','mutb.OBJECTID', '=' ,'toilet_id' )->groupBy('toilet_id')
                ->havingRaw('COUNT(toilet_id) > 0.25*'.$max_visit.' AND COUNT(toilet_id) <= 0.50*'.$max_visit)->get();

            $toiletdetails[2] = ToiletVisits::select( DB::raw('COUNT(toilet_id) as toilet_visits'), 'OBJECTID', 'NAME','lat','lng')
                ->leftjoin('MSDPUSERToilet_Block as mutb','mutb.OBJECTID', '=' ,'toilet_id' )->groupBy('toilet_id')
                ->havingRaw('COUNT(toilet_id) > 0.50*'.$max_visit.' AND COUNT(toilet_id) <= 0.75*'.$max_visit)->get();

            $toiletdetails[3] = ToiletVisits::select( DB::raw('COUNT(toilet_id) as toilet_visits'), 'OBJECTID', 'NAME','lat','lng')
                ->leftjoin('MSDPUSERToilet_Block as mutb','mutb.OBJECTID', '=' ,'toilet_id' )->groupBy('toilet_id')
                ->havingRaw('COUNT(toilet_id) > 0.75*'.$max_visit)->get();

            log::info("number of toilets ".print_r($toiletdetails,true));
            if(sizeof($toiletdetails)>0)
            {
                $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilets fetched");
                return json_encode($data);
            }
            $data=array("status"=>"success","data"=>[], "message"=>"No toilets found in your area");
            return json_encode($data); 
        }

        if($request->criteria==3)
        {
 
            $max_issue1=DB::select('SELECT MAX(t) as max_c From ( select  COUNT(toilet_id) as t FROM Report_Issues group by toilet_id) as max_count')  ;
             Log::info("data test 2" .print_r($max_issue1,true));
            $max_issue = $max_issue1[0]->max_c;

            if(is_null($max_issue) || empty($max_issue)){
                $max_issue = 0;
            }
            Log::info("max_issue".$max_issue);
            $toiletdetails[0] = ReportIssues::select( DB::raw('COUNT(toilet_id) as toilet_visits'), 'OBJECTID', 'NAME','lat','lng')
                ->leftjoin('MSDPUSERToilet_Block as mutb','mutb.OBJECTID', '=' ,'toilet_id' )->groupBy('toilet_id')
                ->havingRaw('COUNT(toilet_id) > 0*'.$max_issue.' AND COUNT(toilet_id) <= 0.25* '.$max_issue)->get();

            $toiletdetails[1] = ReportIssues::select( DB::raw('COUNT(toilet_id) as toilet_visits'), 'OBJECTID', 'NAME','lat','lng')
                ->leftjoin('MSDPUSERToilet_Block as mutb','mutb.OBJECTID', '=' ,'toilet_id' )->groupBy('toilet_id')
                ->havingRaw('COUNT(toilet_id) > 0.25*'.$max_issue.' AND COUNT(toilet_id) <= 0.50* '.$max_issue)->get();

            $toiletdetails[2] = ReportIssues::select( DB::raw('COUNT(toilet_id) as toilet_visits'), 'OBJECTID', 'NAME','lat','lng')
                ->leftjoin('MSDPUSERToilet_Block as mutb','mutb.OBJECTID', '=' ,'toilet_id' )->groupBy('toilet_id')
                ->havingRaw('COUNT(toilet_id) > 0.50*'.$max_issue.' AND COUNT(toilet_id) <= 0.75*'.$max_issue)->get();

            $toiletdetails[3] = ReportIssues::select( DB::raw('COUNT(toilet_id) as toilet_visits'), 'OBJECTID', 'NAME','lat','lng')
                ->leftjoin('MSDPUSERToilet_Block as mutb','mutb.OBJECTID', '=' ,'toilet_id' )->groupBy('toilet_id')
                ->havingRaw('COUNT(toilet_id) >0.75*'.$max_issue)->get();


            log::info("number of toilets ".print_r($toiletdetails,true));
            if(sizeof($toiletdetails)>0)
            {
                $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilets fetched");
                return json_encode($data);
            }
            $data=array("status"=>"success","data"=>[], "message"=>"No toilets found in your area");
            return json_encode($data); 
        }
        

         if($request->criteria==4){
            $toiletdetails=DB::select('SELECT OBJECTID, NAME,lat,lng FROM MSDPUSERToilet_Block WHERE ACTIVE = 0') ;
            log::info("number of toilets ".print_r($toiletdetails,true));
            if(sizeof($toiletdetails)>0)
            {
                $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilets fetched");
                return json_encode($data);
            }
            $data=array("status"=>"success","data"=>[], "message"=>"No toilets found in your area");
            return json_encode($data);
         }

        $data=array("status"=>"success","data"=>[], "message"=>"Such Criteria does not exist");
        return json_encode($data);
      
    }


    


    /**
     * API for entering/updating feedback for specific toilet
     *
     * @param  int  Request $request
     * @return \Illuminate\Http\Response
     */
    public function addComment(Request $request)
    {
        Log::info("feedback");

        $validator = Validator::make($request->all(), [
            'toilet_id' => 'required',
            'toilet_comment' => 'required|string',
            'g_user_id' => 'required|min:4'
        ]);

        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"incomplete comment data");
            return json_encode($data);
        }

        $user = Logins::where('g_user_id',$request->g_user_id)->first();//finding the user from the token
        Log::info("data ".print_r($user,true));
        $userid=UserRegister::where(['email'=>$user->username])->pluck('id');//getting user_id
        
        DB::transaction(function() use ($request, $userid){

        $comment = new Comment();
        $comment->toilet_id = $request->toilet_id;
        $comment->toilet_comment = $request->toilet_comment;
        $comment->user_id = $userid;
        $comment->save();
        });

        Log::info("data the way you want");
        $data=array("status"=>"success","data"=>null, "message"=>"Thank you for your COMMENT");
        return json_encode($data);
      
    }




    // public function getComment(Request $request)
    // {
    //     Log::info("toiletstats");

    //     $validator = Validator::make($request->all(), [
    //         'toiet_id' => 'required',
    //         '' =>  '',
    //     ]);



    //     if ($validator->fails()) 
    //     {
    //         $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"criteria not specified");
    //         return json_encode($data);
    //     }

    //     if($request->criteria==1)
    //     {
    //        $toiletdetails[0]=DB::select('SELECT OBJECTID, NAME,lat,lng FROM MSDPUSERToilet_Block WHERE CONDITION_RAW <2.33') ;

    //        $toiletdetails[1]=DB::select('SELECT OBJECTID, NAME,lat,lng FROM MSDPUSERToilet_Block WHERE CONDITION_RAW BETWEEN 2.33 AND 3.66') ;

    //        $toiletdetails[2]=DB::select('SELECT OBJECTID, NAME,lat,lng FROM MSDPUSERToilet_Block WHERE CONDITION_RAW > 3.66') ;


    //         log::info("number of toilets ".print_r($toiletdetails,true));
    //         if(sizeof($toiletdetails)>0)
    //         {
    //             $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilets fetched");
    //             return json_encode($data);
    //         }
    //         $data=array("status"=>"success","data"=>[], "message"=>"No toilets found in your area");
    //         return json_encode($data); 
    //     }

    //     if($request->criteria==2)
    //     {
    //         $max_visit1=DB::select('SELECT MAX(t) as max_c From ( select  COUNT(toilet_id) as t FROM Toilet_Visits ) as max_count')  ;
    //         Log::info("data test" .print_r($max_visit1,true));
    //         $max_visit = $max_visit1[0]->max_c;
            

    //         if(is_null($max_visit) || empty($max_visit)){
    //             $max_visit = 0;
    //         }

    //         $toiletdetails[0] = ToiletVisits::select( DB::raw('COUNT(toilet_id) as toilet_visits'), 'OBJECTID', 'NAME','lat','lng')
    //             ->leftjoin('MSDPUSERToilet_Block as mutb','mutb.OBJECTID', '=' ,'toilet_id' )->groupBy('toilet_id')
    //             ->havingRaw('COUNT(toilet_id) > 0*'.$max_visit.' AND COUNT(toilet_id) <= 0.25*'.$max_visit)->get();

    //         $toiletdetails[1] = ToiletVisits::select( DB::raw('COUNT(toilet_id) as toilet_visits'), 'OBJECTID', 'NAME','lat','lng')
    //             ->leftjoin('MSDPUSERToilet_Block as mutb','mutb.OBJECTID', '=' ,'toilet_id' )->groupBy('toilet_id')
    //             ->havingRaw('COUNT(toilet_id) > 0.25*'.$max_visit.' AND COUNT(toilet_id) <= 0.50*'.$max_visit)->get();

    //         $toiletdetails[2] = ToiletVisits::select( DB::raw('COUNT(toilet_id) as toilet_visits'), 'OBJECTID', 'NAME','lat','lng')
    //             ->leftjoin('MSDPUSERToilet_Block as mutb','mutb.OBJECTID', '=' ,'toilet_id' )->groupBy('toilet_id')
    //             ->havingRaw('COUNT(toilet_id) > 0.50*'.$max_visit.' AND COUNT(toilet_id) <= 0.75*'.$max_visit)->get();

    //         $toiletdetails[3] = ToiletVisits::select( DB::raw('COUNT(toilet_id) as toilet_visits'), 'OBJECTID', 'NAME','lat','lng')
    //             ->leftjoin('MSDPUSERToilet_Block as mutb','mutb.OBJECTID', '=' ,'toilet_id' )->groupBy('toilet_id')
    //             ->havingRaw('COUNT(toilet_id) > 0.75*'.$max_visit)->get();

    //         log::info("number of toilets ".print_r($toiletdetails,true));
    //         if(sizeof($toiletdetails)>0)
    //         {
    //             $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilets fetched");
    //             return json_encode($data);
    //         }
    //         $data=array("status"=>"success","data"=>[], "message"=>"No toilets found in your area");
    //         return json_encode($data); 
    //     }

    //     if($request->criteria==3)
    //     {
 
    //         $max_issue1=DB::select('SELECT MAX(t) as max_c From ( select  COUNT(toilet_id) as t FROM Report_Issues group by toilet_id) as max_count')  ;
    //          Log::info("data test 2" .print_r($max_issue1,true));
    //         $max_issue = $max_issue1[0]->max_c;

    //         if(is_null($max_issue) || empty($max_issue)){
    //             $max_issue = 0;
    //         }
    //         Log::info("max_issue".$max_issue);
    //         $toiletdetails[0] = ReportIssues::select( DB::raw('COUNT(toilet_id) as toilet_visits'), 'OBJECTID', 'NAME','lat','lng')
    //             ->leftjoin('MSDPUSERToilet_Block as mutb','mutb.OBJECTID', '=' ,'toilet_id' )->groupBy('toilet_id')
    //             ->havingRaw('COUNT(toilet_id) > 0*'.$max_issue.' AND COUNT(toilet_id) <= 0.25* '.$max_issue)->get();

    //         $toiletdetails[1] = ReportIssues::select( DB::raw('COUNT(toilet_id) as toilet_visits'), 'OBJECTID', 'NAME','lat','lng')
    //             ->leftjoin('MSDPUSERToilet_Block as mutb','mutb.OBJECTID', '=' ,'toilet_id' )->groupBy('toilet_id')
    //             ->havingRaw('COUNT(toilet_id) > 0.25*'.$max_issue.' AND COUNT(toilet_id) <= 0.50* '.$max_issue)->get();

    //         $toiletdetails[2] = ReportIssues::select( DB::raw('COUNT(toilet_id) as toilet_visits'), 'OBJECTID', 'NAME','lat','lng')
    //             ->leftjoin('MSDPUSERToilet_Block as mutb','mutb.OBJECTID', '=' ,'toilet_id' )->groupBy('toilet_id')
    //             ->havingRaw('COUNT(toilet_id) > 0.50*'.$max_issue.' AND COUNT(toilet_id) <= 0.75*'.$max_issue)->get();

    //         $toiletdetails[3] = ReportIssues::select( DB::raw('COUNT(toilet_id) as toilet_visits'), 'OBJECTID', 'NAME','lat','lng')
    //             ->leftjoin('MSDPUSERToilet_Block as mutb','mutb.OBJECTID', '=' ,'toilet_id' )->groupBy('toilet_id')
    //             ->havingRaw('COUNT(toilet_id) >0.75*'.$max_issue)->get();


    //         log::info("number of toilets ".print_r($toiletdetails,true));
    //         if(sizeof($toiletdetails)>0)
    //         {
    //             $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilets fetched");
    //             return json_encode($data);
    //         }
    //         $data=array("status"=>"success","data"=>[], "message"=>"No toilets found in your area");
    //         return json_encode($data); 
    //     }
        

    //      if($request->criteria==4){
    //         $toiletdetails=DB::select('SELECT OBJECTID, NAME,lat,lng FROM MSDPUSERToilet_Block WHERE ACTIVE = 0') ;
    //         log::info("number of toilets ".print_r($toiletdetails,true));
    //         if(sizeof($toiletdetails)>0)
    //         {
    //             $data=array("status"=>"success","data"=>$toiletdetails, "message"=>"Toilets fetched");
    //             return json_encode($data);
    //         }
    //         $data=array("status"=>"success","data"=>[], "message"=>"No toilets found in your area");
    //         return json_encode($data);
    //      }

    //     $data=array("status"=>"success","data"=>[], "message"=>"Such Criteria does not exist");
    //     return json_encode($data);
      
    // }



    public function getComment(Request $request)
    {
        Log::info("feedback");

        $validator = Validator::make($request->all(), [
            'toilet_id' => 'required'
        ]);

        if ($validator->fails()) {
            $data=array("status"=>"fail","data"=>$validator->errors(), "message"=>"incomplete data");
            return json_encode($data);
        }

        $comments = Comment::where('toilet_id',$request->toilet_id)
        ->leftJoin('User_Register as ur', 'comment.user_id','=','ur.id')->get();//finding the user from the token
        Log::info("comment data ".print_r($comments,true));
        try{
            
            if(count($comments) >= 1){
                $data=array("status"=>"success","data"=> $comments, "message"=>"comments for the toilet");
                return json_encode($data);
           
            }    
        }
        catch(Exception $e){
            $data=array("status"=>"fail","data"=>0, "message"=>"Something went wrong in reporting the issue, please try again");
        }
        $data=array("status"=>"success","data"=>0, "message"=>"No comment for this toilet");
        return json_encode($data); 
    }




}