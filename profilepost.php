<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Notify.php');
include('./classes/Comment.php');

$userid="";
$loggedInUsername="";
$username="";
$pid = "";
$postimg = "";
$postbody = "";
$likes = "";
$ownerid = "";
if (isset($_GET['username'])) {
	$userid = Login::isLoggedIn();
	if (DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))) {
		$loggedInUsername = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['username'];
	}
	if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))) {
		$username = DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
	}
	if (isset($_GET['postid'])) {
		$post=Post::getPost($_GET['postid']);
		if (count($post)>0) {
			$pid = $post[0]['id'];
			if (strlen($post[0]['postimg'])>0) {
				$postimg = $post[0]['postimg'];
			}else{
				$postimg = 'images/nopreview.png';
			}
			$postbody = $post[0]['body'];
			$likes = $post[0]['likes'];
			$ownerid = $post[0]['user_id'];
		}
	}
}else{
  die("<img align='middle' style='padding: 100px 400px;' src='images/404.jpg'>");
}

	?>
	<!DOCTYPE html>
	<html>
	<head>
		<!-- Import Google Icon Font -->
		<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<!-- Import materialize.css -->
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

		<!-- <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"> -->

		<!-- Let browser know website is optimized for mobile -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	</head>

	<body>

<div class="container">
	<div class="row">
		<div class="col s6 offset-s3">
			<div class="card">
			<span class="card-title" style="width:20px; height: 10px;"><a href="profile.php?username=<?php echo $username; ?>"><font style="font-size: 15px; margin-left: 20px">Return</font></a></span>
				<div style="height:380px; margin-top: 30px" class="card-image">
					
					<img style="height:350px;width:90%; margin: 0 auto;" src="<?php echo $postimg;?>">
				</div>
				<div class="card-content">
					<p><?php echo $postbody;?></p>
				</div>
				<div class="card-action">
				<a href="#"><img src="images/heart-empty.png" style="width:20px;"> 233</a>
					<!-- Modal Trigger -->
					<a class="modal-trigger" href="#modal1"><i class="material-icons" style="color: grey;margin-left: 40px; ">comment</i></a>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Structure -->
<div id="modal1" class="modal modal-fixed-footer">
	<div class="modal-content" >
		<h6>Comments</h6>
		<ul style="width: 650px" class="collection">
			<li style="height: 40px" class="collection-item avatar">
				<i class="material-icons">account_circle</i>
				<span class="title">Title</span>
				<p>First Line</p>
			</li>
			<li style="height: 40px" class="collection-item avatar">
				<i class="material-icons">account_circle</i>
				<span class="title">Title</span>
				<p>First Line</p>
			</li>
			<li style="height: 40px" class="collection-item avatar">
				<i class="material-icons">account_circle</i>
				<span class="title">Title</span>
				<p>First Line</p>
			</li>
			<li style="height: 40px" class="collection-item avatar">
				<i class="material-icons">account_circle</i>
				<span class="title">Title</span>
				<p>First Line</p>
			</li>
		</ul>
	</div>
	<div class="modal-footer">
		<form class="col s12">
			<div class="row">
				<div class="input-field col s6">
					<i class="material-icons prefix">account_circle</i>
					<input id="icon_prefix" type="text" class="validate">
					<!--           <label for="icon_prefix">Comment</label> -->
					<input type="submit" name="comment" value="Comment">
				</div>
			</div>
		</form>
	</div>
</div>     
</body>
</html>