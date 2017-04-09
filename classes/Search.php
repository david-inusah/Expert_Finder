<?php
/**
* 
*/
class Search
{	
	public static function recoSearch($username)
	{$skills="";

		$skills_array = DB::query('SELECT `skill` from skills,user_skills,users WHERE username=:username and users.id=user_skills.user_id and user_skills.skill_id=skills.id', array(':username'=>$username));
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

		return $users;
	}
	// public static function userSearch($username){

	// }
}
?>
