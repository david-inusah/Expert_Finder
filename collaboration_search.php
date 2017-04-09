<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/SearchRank.php');
include('./classes/Notify.php');

$userid="";
$username="";
if (Login::isLoggedIn()) {
	$userid = Login::isLoggedIn();
	if (DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))) {
		$username = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['username'];
	}
}
else{
	die('User not logged');
}

if (isset($_POST['collaboration_searchbox'])) {
	$keyword = explode(" ", $_POST['collaboration_searchbox']);
        // for ($i=0;$i<count($keyword);$i++) {
        // 	if ($i==0){
	$tosearch=str_split($keyword[0], 2);
        	// }else{
        	// 	$tosearch1=str_split($keyword[$i], 3);
        	// 	for ($p=0;$p<count($tosearch1);$p++) {
        	// 		array_push($tosearch, $tosearch1[$i]);		
        	// 	}
        	// }
        // }
	$whereclause = "";
	$experts="";
	$paramsarray = array(':userid'=>$userid,':keyword'=>'%'.$_POST['collaboration_searchbox'].'%');
	for ($i = 0; $i < count($tosearch); $i++) {
		if (strlen($tosearch[$i])==2){
			$whereclause .= " OR username LIKE :u$i ";
			$paramsarray[":u$i"] = $tosearch[$i];
		}
	}
        // echo "$whereclause";
        // echo "<pre>";
        // print_r($paramsarray);
        // echo "<pre>";
	$experts = DB::query('SELECT users.username, skills.skill, users.worklocation  FROM users, skills, user_skills WHERE user_skills.user_id=users.id AND user_skills.skill_id=skills.id AND users.id!=:userid AND (skills.skill LIKE :keyword'.$whereclause.')', $paramsarray);
	// echo "<pre>";
	// print_r($experts);
	// echo "<pre>";
// }	
	$searchername= DB::query('SELECT username FROM users WHERE id=:userid',array(':userid'=>$userid))[0]['username'];
	$searcherworklocation = DB::query('SELECT worklocation FROM users WHERE id=:userid',array(':userid'=>$userid))[0]['worklocation'];

	echo "Searching User:"."</br>";
	echo $searchername.'</br>';	
	echo $searcherworklocation.'</br>';
	echo "</br></br>";

	if (count($experts)>0) {
		// echo "string";
		foreach ($experts as $key) {
			$expert= $key['username'];
			echo $expert;
			$expertskill = $key['skill']."</br>";
			$expertworklocation = $key['worklocation'];
			echo "string";
		// echo "</br></br>";
			SearchRank::getDistanceMatrix($expert, $searcherworklocation,$expertworklocation);
			SearchRank::displayDetails();
		// echo "</br></br>";
		// 	echo "<div style='width: 110px; color: navy; background-color: pink; border: 2px solid blue; padding: 5px;'>
		// 	Found expert</br>
		// 	$expert.</br>
		// 	$expertskill
		// 	$expertworklocation.</br>
		// </div></br>";
		}
	}else{
		echo "Sorry <b><?php echo $username;?></b>, there are no users with this interests right now :)";
	}
		// $obj->getDistanceMatrix();
		// echo $response.'ssasas';

// $oJSON=json_decode($response);
// if ($oJSON->status=='OK')
//         $fDistanceInMiles=(float)preg_replace('/[^\d\.]/','',$oJSON->rows[0]->elements[0]->distance->text);
// else
//         $fDistanceInMiles=0;

// echo 'Distance in Miles: '.$fDistanceInMiles.PHP_EOL;

}
?>
<!DOCTYPE html>
<html>
<head>
	<!-- Import Google Icon Font -->
	<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<!-- Import materialize.css -->
	<link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>

	<!-- Let browser know website is optimized for mobile -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

	<script src="https://code.jquery.com/jquery-3.2.1.min.js"
	integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
	crossorigin="anonymous"></script>    

	<!-- Compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.1/css/materialize.min.css">

	<!-- Compiled and minified JavaScript -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.1/js/materialize.min.js"></script>
	<!--Let browser know website is optimized for mobile-->
	<script type="text/javascript" src="js/modals.js" ></script>
</head>

<body>
	<!-- <img src="images/searchpage.jpg" class="center-align"> -->

	
    <nav>
        <div class="nav-wrapper black darken-1"">
          <a href="#" class="brand-logo"><h5>Patatte</h5></a>
          <ul id="nav-mobile" class="right hide-on-med-and-down">
            <li class="active"><a href="profile.php?username=<?php echo $username; ?>"><i class="material-icons">person</i></a></li>
            <li><a href="index.php">News Feed</a></li>
            <li><a href="collaboration_search.php">Collaborate</a></li>
            <li><a href="#!" class="collection-item dropdown-button"><span class="new badge"><?php echo Notify::notificationsCount($userid);?></span></a></li>
            <!-- <li><a class="dropdown-button" href=""><i class="material-icons">notifications</i></a></li> -->
            <li><a class="dropdown-button" href="#!"> <i class="material-icons">more_vert</i></a></li>
            <!-- <li></li> -->
        </ul>
    </div>
</nav>

	<h4>Search by interest to find other experts to collaborate with here</h4>
	<form action="collaboration_search.php" method="post">
		<input type="text" name="collaboration_searchbox" value="">
		<input type="submit" name="collab_search" value="Search">
	</form>

<!--   <h3>News Feed</h3>
-->
<!-- <!--Import jQuery before materialize.js -->
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
</body>
</html>