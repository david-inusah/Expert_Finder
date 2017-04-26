<?php
/**
* 
*/
class Search
{	
	public static function recoSearch($username){
        $users=false;
        if (strlen($username)!=0) {
            $skills="";
            $skills_array = DB::query('SELECT `skill` from skills,user_skills,users WHERE username=:username and users.id=user_skills.user_id and user_skills.skill_id=skills.id', array(':username'=>$username));
            if (sizeof($skills_array)!=0) {
                $skill1=$skills_array[0][0];
                for ($i=0; $i < count($skills_array); $i++) { 
                   if ((count($skills_array)-$i)==2) {
                    $skills.=$skills_array[$i][0].',';
                }else{
                    $skills.=$skills_array[$i][0];
                }
            }
            $array = explode(",", $skills);
            echo '</br>';
            $whereclause = "";
            $paramsarray = array(':username'=>$username, ':skill'=>$skill1);
            for ($i = 1; $i < count($array); $i++) {
               $whereclause .= " OR skill LIKE :u$i ";
               $paramsarray[":u$i"] = $array[$i];
           }
           $users = DB::query('SELECT users.username, users.bio FROM users, skills, user_skills WHERE users.id=user_skills.user_id AND user_skills.skill_id=skills.id AND users.username!=:username AND (skills.skill =:skill'.$whereclause.')', $paramsarray);
       }
   }
   return $users;
}
	public static function userSearch($tosearch){
        if (count($tosearch) == 1) {
            $tosearch = str_split($tosearch[0], 2);
        }
        $whereclause = "";
        $paramsarray = array(':username'=>'%'.$_POST['searchbox'].'%');
        for ($i = 0; $i < count($tosearch); $i++) {
            $whereclause .= " OR username LIKE :u$i ";
            $paramsarray[":u$i"] = $tosearch[$i];
        }
        $users = DB::query('SELECT users.username FROM users WHERE users.username LIKE :username '.$whereclause.'', $paramsarray);
        // print_r($users);
        foreach ($users as $key) {
            $reco_username=$key['username'];
            $userLink=Post::link_add('@'.$reco_username);
            echo "<div style='width: 90px; color: navy; background-color: pink; border: 2px solid blue; padding: 5px;'>
            <p>".$userLink."</p>
        </div></br>";
    }

	}
public static function expertSearch($keyword,$userid){
    $experts=array();
    if (count($keyword)!=0) {
        $tosearch=str_split($keyword[0], 2);
    $whereclause = "";
    $experts="";
    $paramsarray = array(':userid'=>$userid,':keyword'=>'%'.$_POST['collaboration_searchbox'].'%');
    for ($i = 0; $i < count($tosearch); $i++) {
        if (strlen($tosearch[$i])==2){
            $whereclause .= " OR username LIKE :u$i ";
            $paramsarray[":u$i"] = $tosearch[$i];
        }
    }
    $experts = DB::query('SELECT users.username, skills.skill, users.worklocation, users.profileimg  FROM users, skills, user_skills WHERE user_skills.user_id=users.id AND user_skills.skill_id=skills.id AND users.id!=:userid AND (skills.skill LIKE :keyword'.$whereclause.')', $paramsarray);
    }
    return $experts;
}

public static function getDistanceMatrix($expert, $origin, $destination){
    $miniresult="";
    $origin=urlencode($origin);
    $destination=urlencode($destination);
    $url="https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=".$origin."&destinations=".$destination."&key=AIzaSyCQcqwU8Akzv93zlX5EJEeKwDYT12D3I3Y";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = json_decode(curl_exec($ch), true);
    
    if($response['status'] == "OK"){
        $dist = $response['rows'][0]['elements'][0]['distance']['text'];
        $dur = $response['rows'][0]['elements'][0]['duration']['text'];
        $miniresult='<h6 class="cyan-text text darken-3"><b>'.$dist.' away</br></h6>
        <h6 class="cyan-text text darken-3"><b>'.$dur.' driving </b></h6>';
    }
    return $miniresult;
}

}
?>
