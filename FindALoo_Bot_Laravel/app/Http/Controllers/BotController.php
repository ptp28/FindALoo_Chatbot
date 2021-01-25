<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Log;
use DB;

class BotController extends Controller
{
    public function index()
    {
        //
    }

    public function getBotRequest(Request $request)
    {
        Log::info($request);

        $chatID = strval($request["message"]["from"]["id"]);
        if (array_key_exists("location", $request["message"]))
        {
            $latitude = strval($request["message"]["location"]["latitude"]);
            $longitude = strval($request["message"]["location"]["longitude"]);
            $this->sendTextMessage($chatID, "<b>"."FETCHING NEARBY PUBLIC TOILETS 🚽🧴..."."</b>");
            $data = $this->getToilets($latitude,$longitude);


            if($data["status"] == "success")
            {
                foreach($data["data"] as $toilet_data)
                {
                    $toiletData = "<b>[".$toilet_data['OBJECTID']."]</b>"."\n".
                        $toilet_data['NAME']."\n".
                        $toilet_data['address']."\n".
                        "<b>".substr($toilet_data['distance'],0,4)."kms</b>"."\n".
                    "\n".
                    "<b> CLICK HERE TO GET DIRECTIONS 👉 </b> /".$toilet_data['OBJECTID'];

                    $this->sendTextMessage($chatID, urlencode($toiletData));
                }

//                $instruction = "<b>"."Type /[ID] to get the directions to that toilet."."</b>"."\n".
//                    "For example - "."\n".
//                    "Type '/".$data["data"][0]['OBJECTID']."' to get details about toilet ID 123";
//                $this->sendTextMessage($chatID, urlencode($instruction));
            }
        }
        else if(preg_match('/^\/[0-9]+$/', $request["message"]["text"]))
        {
            $toiletID = substr($request["message"]["text"],1);
            $this->sendTextMessage($chatID, "Fetching toilet details ...");
            $toilet_data = $this->getToilet($toiletID);

            $toiletDataMessage = "<b>[".$toilet_data['OBJECTID']."]</b>"."\n".
                $toilet_data['NAME']."\n".
                $toilet_data['ADDRESS']."\n".
                $toilet_data['free']."\n".
                $toilet_data['dr_water']."\n".
                $toilet_data['men']."\n".
                $toilet_data['women']."\n".
                $toilet_data['disabled']."\n".
                $toilet_data['western']."\n".
                $toilet_data['CONDITION']."\n";

            $this->sendTextMessage($chatID, urlencode($toiletDataMessage));
            $this->sendLocationMessage($chatID, $toilet_data['lat'], $toilet_data['lng']);
        }
        else
        {
            $this->sendTextMessage($chatID, "Use one of the '/' commands");
        }
    }

    public function sendLocationMessage($chatID, $latitude, $longitude)
    {
        $messageContent = sprintf("sendLocation?chat_id=%s&latitude=%s&longitude=%s.",$chatID,$latitude,$longitude);
        $status = $this->sendBotMessage($messageContent);
        if($status == 200)
            return 1;
        else
            return 0;
    }

    public function sendTextMessage($chatID, $textMessage)
    {
        $messageContent = sprintf("sendMessage?parse_mode=HTML&chat_id=%s&text=%s.",$chatID,$textMessage);
        $status = $this->sendBotMessage($messageContent);
        if($status == 200)
            return 1;
        else
            return 0;
    }

    private function sendBotMessage($messageContent)
    {
        $token = strval(env('TELEGRAM_API_KEY'));
        $base_url = sprintf("https://api.telegram.org/bot%s/", $token);
        $url = $base_url.$messageContent;
        error_log($url);
        if(!empty($messageContent))
        {
            $response = Http::get($url);
            if($response->status() != 200)
            {
                Log::error("ERROR IN SENDING MESSAGE");
                Log::error($response);
            }
            return $response->status();
        }
    }

    public function getToilets($latitude, $longitude)
    {
        $count=10; //keeping default toilet list size as 20
        $radius=1.5;//keeping default distance as 1.5 km

        $toiletdetails=DB::select('SELECT OBJECTID, NAME,lat,lng, MSDPUSERToilet_Block.CONDITION, CONDITION_RAW, ACTIVE, address, ( 6371 * acos( cos( radians(:user_lat1) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(:user_lng) ) + sin( radians(:user_lat2) ) * sin( radians( lat ) ) ) ) AS distance FROM MSDPUSERToilet_Block WHERE ACTIVE = 1 HAVING distance < :rad order by distance asc limit :count',['user_lat1'=>$latitude,'user_lat2'=>$latitude,'user_lng'=>$longitude,'rad'=>$radius, 'count'=>$count]);

        $toiletdetails = json_decode(json_encode($toiletdetails),true); //needed to convert array of objects to array of arrays

        if(sizeof($toiletdetails)>0)
        {
            $data=array("status"=>"success","data"=>$toiletdetails, "size"=> sizeof($toiletdetails));
        }
        else
        {
            $data=array("status"=>"fail","data"=>null, "size"=> 0);
        }
//        Log::info("GETTING TOILETS".$latitude."-".$longitude);
//        Log::info($data);
        return $data;
    }

    public function getToilet($objectID)
    {
        $toiletData = DB::table('MSDPUSERToilet_Block')
            ->where(['OBJECTID' => $objectID])
            ->first();
        $toiletData = json_decode(json_encode($toiletData),true); //needed to convert array of objects to array of arrays
        return $toiletData;
    }
}
