<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\MSDPToiletRegister;
use App\Models\Logins;
use DB;
use Log;
use DateTime;

class AdminController extends Controller
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

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
    public function show($id)
    {
        //
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



     public function updateToilet($token,$id,$active)
    {
        $id1= $id;
        Log::info("id valeu".$id1);
        if(!$token){
            $data=array("status"=>"error","data"=>null, "message"=>"Cannot Update the toilet Status");
            return response()->json($data);
        }
        

        try{
            $exception =DB::transaction(function() use ($id, $token, $active) {
                $toilet= MSDPToiletRegister::where('OBJECTID',$id)->where('token',$token)->first();
                if(!$toilet){
                    $data=array("status"=>"fail","data"=>null, "message"=>"Toilet not found/Wrong id");
                    return response()->json($data);
                }
       
                    $toilet->active=$active;
                    $toilet->token = NULL;
                    $toilet->save();

                    if(!$toilet->save()){
                        $data=array("status"=>"fail","data"=>null, "message"=>"Something went in updating the toilet status, please try again");
                        return response()->json($data);
                    }
                 
            });//transaction ends here
            if(is_null($exception)){
                    $data=array("status"=>"success","data"=>null, "message"=>"Toilet status updated");
            }
        }
        catch(Exception $e){
            $data=array("status"=>"fail","data"=>null, "message"=>"Something went in updating the toilet status, please try again");
            DB::rollback();
        }
        
        $data=array("status"=>"success","data"=>null, "message"=>"Toilet status updated");
        return response()->json($data);
    }

    public function luckyDraw(){
        $now   = new DateTime();
        $now->format('Y-m-d H:i:s');
        $end = new DateTime();
        $end->format('Y-m-d H:i:s');
        $end->modify('+1 day');
        $count = Logins::select('username')->where('created_at','>=',$now)->where('created_at','<',$end)->get()->count();
        $alreadyDraw = Logins::select('username')
        ->where('created_at','>=',$now)
        ->where('created_at','<',$end)
        ->where('lucky',1)
        ->lists('username')
        ->toArray();
        return view('lucky_draw')->with(['count'=>$count, 'lucky_draw_user'=>$alreadyDraw]);
    }

    public function luckyDrawResult(){
        $now   = new DateTime();
        $now->format('Y-m-d H:i:s');
        $end = new DateTime();
        $end->format('Y-m-d H:i:s');
        $end->modify('+1 day');
        $users = Logins::select('username')
        ->where('created_at','>=',$now)
        ->where('created_at','<',$end)
        ->where('lucky',0)
        ->lists('username')
        ->toArray();
        $alreadyDraw = Logins::select('username')
        ->where('created_at','>=',$now)
        ->where('created_at','<',$end)
        ->where('lucky',1)
        ->lists('username')
        ->toArray();
        $count = count($users);
        if($count > 0){
            $user = $users[array_rand($users)];
            $update = Logins::where('username',$user)->first();
            $update->lucky = false;
            $update-> save();
        }
        else
            $user = "No user found";
        return view('lucky_draw')->with(['data'=>$user,'count'=>$count, 'lucky_draw_user'=>$alreadyDraw ]);
    }
}
