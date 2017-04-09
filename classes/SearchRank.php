<?php
/**
* 
*/
class SearchRank
{
        private $expertname;
        private $user_location;
        private $expert_location;
        private $distance;
        private $duration;

        function __construct($expert, $userloc, $expertloc)
        {
                $this->expertname = $expert;
                $this->user_location = $userloc;
                $this->expert_location = $expertloc;

        }

        public static function getDistanceMatrix($expert, $origin, $destination){
                // $origin;
                // $destination;
                // $url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=".$origin."&destinations=".$destination."&key=AIzaSyCQcqwU8Akzv93zlX5EJEeKwDYT12D3I3Y";
                $url='https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins='.$origin.'&destinations='.$destination.'&key=AIzaSyCQcqwU8Akzv93zlX5EJEeKwDYT12D3I3Y';   
                // echo $url;     
                $ch = curl_init();
                $bool = curl_setopt($ch, CURLOPT_URL, $url);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = json_decode(curl_exec($ch), true);
                echo $response;
    //If google responds with a status of OK
    //Extract the distance text:
                echo $bool;
                if($response['status'] == "OK"){
                        $dist = $response['rows'][0]['elements'][0]['distance']['text'];
                        echo "<p>Dist: $dist</p>";
                        $dist2 = $response['rows'][0]['elements'][1]['distance']['text'];
                        echo "<p>Dist2: $dist2</p>";
                // return $arr;

                }else{
                        echo "nope. didnt get through";
                }
        }

        function getDistance(){
                return $distance;
        }

        function getDuration(){
                return $duration;
        }

        public static function displayDetails(){
                $details = "Expert name: David</br>Workshed location:  Ghana</br>Distance from you: ".getDistance()."</br>Average time from you: ".getDuration()."</br>";
                // echo "Drumming";
                return $details;
        }

        
}
?>