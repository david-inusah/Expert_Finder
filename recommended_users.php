<?php
include('./classes/Login.php');
include('./classes/DB.php');
include('./classes/Post.php');
include('./classes/FollowUser.php');
include('./classes/Search.php');

// if (isset($_GET['username'])) {
$username="";
$following="";
	// $skills="";
	$users="beans";
if (isset($_GET['username'])&& strlen($_GET['username'])>0) {
	if(Login::isLoggedIn()){
		$userid=Login::isLoggedIn();
		if (Login::firstLogin($userid)) {
			$username = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['username'];
			$users=Search::recoSearch($_GET['username']);
		}
	}else{
		die("<img align='middle' style='padding: 100px 400px;' src='images/404.jpg'>");
	}


	if (isset($_GET['follow'])) {
		$reco_username=$_GET['reco_username'];
		$reco_userid = DB::query('SELECT id FROM users WHERE username=:reco_username', array(':reco_username'=>$reco_username))[0]['id'];
		if (FollowUser::follow($reco_userid, $userid)){
			$following=$reco_username;
		}

	}
// $username=$_GET['username'];
}else{
	die("<img align='middle' style='padding: 100px 400px;' src='images/404.jpg'>");

}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">

	<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>

	<script src="https://code.jquery.com/jquery-3.2.1.min.js"
	integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
	crossorigin="anonymous"></script>    

	<!-- Compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.1/css/materialize.min.css">

	<!-- Compiled and minified JavaScript -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.1/js/materialize.min.js"></script>
	<!--Let browser know website is optimized for mobile-->
	<script type="text/javascript" src="js/modals.js" ></script>

	<style type="text/css">
		body{
			/*background-image: url('images/chips.jpg');*/
			background-repeat: no-repeat;
			background-position: 50% 50%;
			background-clip: padding-box;
		}
	</style>
</head>

<body>
	<!-- <nav>
		<div class="nav-wrapper">
			<a href="#" class="brand-logo">Patatte</a>
			<ul id="nav-mobile" class="right hide-on-med-and-down">
			<span>Recommended Users</span>
			</ul>
		</div>
	</nav>
	<div> -->

	</div>
	<?php
	if ($users && $users!='beans') {
		echo '<div class="card-panel teal lighten-2">
		<h4>Hey <b>'.$username.'</b>, here are some users with similar interests we recommend you follow!</h4>
	</div>

	<div class="row" style="margin-left:70px;">';
		foreach ($users as $key) {
			$reco_username=$key['username'];
			$userLink=Post::link_add('@'.$reco_username);
			$firstname= DB::query('SELECT firstname FROM users WHERE username=:username', array(':username'=>$reco_username))[0]['firstname'];
			$lastname=DB::query('SELECT lastname FROM users WHERE username=:username', array(':username'=>$reco_username))[0]['lastname'];
			
			echo '<div class="col s4" style="width:300px;height:500px;">
			<div class="card cyan darken-3" >
				<div class="card-content">
					<h6 class="cyan-text text darken-3"><b>View Profile:</b> <a class="orange-text text darken-4" href="profile.php?username='.$reco_username.'">'.$firstname.' '.$lastname.'</a></h6>
				</div>
				<div style="height:200px;" class="card-image responsive-img"><a href="profile.php?username='.$reco_username.'">
					<img style="width:200px; margin:0 auto;" src="images/profile.png"></a>
				</div>
				<div class="card-content" style="text-align:center;">
					<h6 class="cyan-text text darken-3"><b>Bio: </b>'.$key['bio'].'</h6> 

				</div>
				<div class="card-action">';

					if ($following==$reco_username) {
						echo '<a style="margin-left: 15px" class="disabled waves-effect waves-light btn">following</a>';
					}else{
						echo '<a style="margin-left: 15px" href="recommended_users.php?follow&username='.$username.'&reco_username='.$reco_username.'" class="waves-effect waves-light btn">follow</a>	';
					}
					echo '</div>
				</div>
			</div>';


			// echo '<div class="col s2">
   //            <div class="card transparent" >
   //            <div class="card-content">
   //                    <h6 class="cyan-text text darken-3">Checkout this user\'s profile:<br><br>'.$userLink.'</h6>
   //                </div>
   //                <div style="height:200px;" class="card-image responsive-img">
   //           <img src="images/profile.png">
   //                  </div>
   //                  <div class="card-content">
   //                    <h6 class="cyan-text text darken-3"><b>Bio: </b>'.$key['bio'].'</h6>
   //                </div>
   //                <div class="card-action">';

   //                    if ($following==$reco_username) {
   //                    	echo $reco_username.'<br>';
   //                    	echo $following.'done';
   //                    	echo '<a style="margin-left: 15px" class="disabled waves-effect waves-light btn">following</a>fdfdfdfdfdf';
   //                    }else{
   //                    	echo $reco_username.'<br>';
   //                    	echo $following.'not done';
   //                    	echo '<a style="margin-left: 15px" href="recommended_users.php?follow&username='.$username.'&reco_username='.$reco_username.'" class="waves-effect waves-light btn">follow</a>	';
   //                    }
   //                 echo '</div>
   //              </div>
   //          </div>';
		}}elseif ($users=='beans') {
	die("<img align='middle' style='padding: 100px 400px;' src='images/404.jpg'>");
		}else{
			echo '<div class="card-panel teal lighten-2">
			<h4>Sorry <b>'.$username.'</b>, there are no users with this interests right now :)</h4>
		</div>
		';
	}
	echo '</div>';
	?>
	<h6 class="cyan-text darken-3" style="text-align: center;">Account Details Progress: Complete!</h6><br>
	
	<div class="progress" style="width: 500px; margin: 0 auto;">
		<div class="determinate" style="width: 100%"></div>
	</div>
</div><br><br>
<center>	<form method="Post" action="profile.php?username=<?php echo $username; ?>" class="col s12">
	<div class='row' style="width: 100px; margin: 0 auto;">
		<button type='submit' name='done' class='col s12 btn btn-small waves-effect teal lighten-2'>Done</button>
	</div>
</center><br><br></form>



	<!-- <form action="profile.php?username=<'?php echo $username; ?>" method="post">
		<input type="submit" name="done" value="Done">
	</form> -->

	<!-- <!--Import jQuery before materialize.js -->
	<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="js/materialize.min.js"></script>
</body>
</html>