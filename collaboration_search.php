<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Search.php');
include('./classes/Notify.php');


$userid="";
$username="";
$results="";

if (Login::isLoggedIn()) {
	$userid = Login::isLoggedIn();
	if (DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))) {
		$username = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['username'];
	}
}
else{
	die("<img align='middle' style='padding: 100px 400px;' src='images/404.jpg'>");
}

if (isset($_POST['collaboration_searchbox'])) {
	if(strlen($_POST['collaboration_searchbox'])!=0){
		$keyword = explode(" ", $_POST['collaboration_searchbox']);
		$experts=Search::expertSearch($keyword,$userid);
		$searchername= DB::query('SELECT username FROM users WHERE id=:userid',array(':userid'=>$userid))[0]['username'];
		$searcherworklocation = DB::query('SELECT worklocation FROM users WHERE id=:userid',array(':userid'=>$userid))[0]['worklocation'];

		if (count($experts)>0) {
			$results.= '<div class="row" style="margin-left:70px;">';

			foreach ($experts as $key) {
				$expert= $key['username'];
				$expertskill = $key['skill']."</br>";
				$expertworklocation = $key['worklocation'];
				$profileimg = $key['profileimg'];
				$distdur = Search::getDistanceMatrix($expert, $searcherworklocation,$expertworklocation);
				$firstname= DB::query('SELECT firstname FROM users WHERE username=:username', array(':username'=>$expert))[0]['firstname'];
				$lastname=DB::query('SELECT lastname FROM users WHERE username=:username', array(':username'=>$expert))[0]['lastname'];
				$results.= '<div class="col s3" style="width:300px;height:500px;">
				<div class="card cyan darken-3" >
					<div class="card-content">
						<h6 class="cyan-text text darken-3" style="text-align:center;"><b>Collaborate with:</b></h6><h6 style="text-align:center;"> <a class="orange-text text darken-4" href="profile.php?username='.$expert.'">'.$firstname.' '.$lastname.'</a></h6>
					</div>
					<div style="height:200px;" class="card-image responsive-img"><a href="profile.php?username='.$expert.'">';
						if (strlen($profileimg)==0) {
							$results.= '<img style="width:200px; margin:0 auto;" src="images/profile.png"></a>';
						}else{
							$results.= '<img style="width:200px; height:200px; margin:0 auto;" class="circle" src="'.$profileimg.'"></a>';
						}
						$results.='</div>
						<div class="card-content" style="text-align:center;">
							<h6 class="cyan-text text darken-3">'.$key['skill'].'</h6>
							<h6 class="cyan-text text darken-3">'.$key['worklocation'].'</h6> '.$distdur.'</div>
						</div>
					</div>';
				}
			}else{
				$results.="none";
			}
		}
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
		<link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>

		<script type="text/javascript" src="js/modals.js" ></script>


		<!-- Compiled and minified JavaScript -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.1/js/materialize.min.js"></script>
		<!--Let browser know website is optimized for mobile-->
		<script type="text/javascript" src="js/modals.js" ></script>
		<style type="text/css">

			body {
				/*background-image: url("images/chips.png");*/
				background-color:#b2dfdb;
				background-repeat: no-repeat;
				background-position: 200px 30px;
				background-clip: padding-box;
				/*background: #fff;*/
			}
		</style>
	</head>

	<body>
		<!-- <img src="images/searchpage.jpg" class="center-align"> -->


		<nav>

			<ul id="dropdown1" class="dropdown-content">
				<li><a href="my-account.php">Account Settings</a></li>
				<li><a href="logout.php">Logout</a></li>
				<li class="divider"></li>
			</ul>

			<div class="nav-wrapper black darken-2"">
				<a href="index.php" class="brand-logo" style="margin-left: 20px"><h5>Patatte</h5></a>
				<ul id="nav-mobile" class="right hide-on-med-and-down">
					<li class="active"><a href="profile.php?username=<?php echo $username; ?>"><i class="material-icons">person</i></a></li>
					<li><a href="index.php">News Feed</a></li>
					<li class="active"><a href="collaboration_search.php">Collaborate</a></li>
					<li><a href="http://www.patatte.com/">Blog</a></li>
					<li><a href="notify.php"><span class="new badge"><?php echo Notify::newnotificationsCount($userid);?></span></a></li>
					<li><a href="my-account.php"><i class="material-icons">settings</i></a></li>
					<li><a href="logout.php">Logout</a></li>
				</ul>
			</div>
		</nav>


		<div class="card-panel teal lighten-2">
			<h4 style="text-align: center;">Search by <b>skills</b> to find other <b>experts</b></h4>

		</div>

		<div style="width: 90%; margin: 0 auto; padding-top: 40px">
			<form action="collaboration_search.php?collab_search" method="post">
				<div style="height: 70px; text-align: center; padding-top: 10px" class="card-panel">
					<!-- <div> -->
					<label><i class=" small material-icons">search</i></label>
					<!-- </div> -->
					<input style="width: 85%; "  type="text" name="collaboration_searchbox" value="">
				</div><!-- <input type="submit" name="collab_search" value="Search"> -->
			</form>
		</div>

		<?php
		if ($results!='none'){
			echo $results;
		}else{
			echo '<div style="height: 100%; width: 100%; text-align: center;">
			<br><br><font style="font-size:30px; color: #9e9e9e ;">Sorry, we did not find any experts with this skill.</font><h3 style="font-size:30px; color: #9e9e9e ;text-align:center;">:(</h3>
		</div>';
	}
	?>
<!--   <h3>News Feed</h3>
-->
<!-- <!--Import jQuery before materialize.js -->
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
</body>
</html>