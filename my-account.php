<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Image.php');
$username="";
$userid="";
$result1="";
$result2="";
if (Login::isLoggedIn()) {
	$userid = Login::isLoggedIn();
	    $username = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['username'];

	if (isset($_POST['uploadprofileimg'])) {
	$result2.=Image::uploadImage('profileimg', "UPDATE users SET profileimg = :profileimg WHERE id=:userid", array(':userid'=>$userid));
}
 if (isset($_POST['changepassword'])) {
                $oldpassword = $_POST['oldpassword'];
                $newpassword = $_POST['newpassword'];
                $newpasswordrepeat = $_POST['newpasswordrepeat'];
                if (password_verify($oldpassword, DB::query('SELECT password FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['password'])) {
                        if ($newpassword == $newpasswordrepeat) {
                                if (strlen($newpassword) >= 6 && strlen($newpassword) <= 60) {
                                        DB::query('UPDATE users SET password=:newpassword WHERE id=:userid', array(':newpassword'=>password_hash($newpassword, PASSWORD_BCRYPT), ':userid'=>$userid));
                                        $result1.= 'Done!';
                                }
                        } else {
                                $result1.= 'Passwords don\'t match!';
                        }
                } else {
                        $result1.= 'Incorrect old password!';
                }
        }
} else {
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

	<!--   <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"> -->

	<script src="libs/SendBird-SDK-JavaScript/SendBird.min.js"></script>
	<!--Let browser know website is optimized for mobile-->
	<script type="text/javascript" src="js/modals.js" ></script>
</head>

<body>
<div class="container">
<h4>My Account Settings</h4><br>
		<div class="card grey lighten-5" style="width: 90%;height: 470px;margin:0 auto; padding-top: 10px;padding-left: 10px">
		<h6 class="left-align" style="padding-bottom: 50px"><a href="profile.php?username=<?php echo $username; ?>">To Profile page</a></h6>
			<div class="grey lighten-2 card-panel" style="height:100px;width:70%; margin:0 auto;margin-bottom: 70px">
			<a class="modal-trigger" href="#modal1"><h6 class="center-align"><i class="tiny material-icons">edit</i>   Password change</h6></a><h6 class="center-align" style="color: #00e676  ;"><?php echo $result1;?></h6>
			<div id="modal1" class="modal modal-fixed-footer">
				<div class="modal-content">
					<h6>Change your Password</h6><br><br><br>
<form action="my-account.php" method="post">
		<input type="password" name="oldpassword" value="" placeholder="Current Password"><p/>
        <input type="password" name="newpassword" value="" placeholder="New Password"><p/>
        <input type="password" name="newpasswordrepeat" value="" placeholder="Repeat Password"><p/>
				</div>
				<div class="modal-footer">
				 <input type="submit" class="btn" name="changepassword" value="Change Password">
</form>
				</div>
			</div> 
		</div>    

			<div class="grey lighten-2 card-panel" style="height:100px;width:70%; margin:0 auto;">
			<a class="modal-trigger" href="#modal2" style="height:100px;width:70%"><h6 class="center-align"><i class="tiny material-icons">edit</i>   Upload new profile picture</h6></a><h6 class="center-align" style="color: #00e676  ;"><?php echo $result2;?></h6>
			<div id="modal2" class="modal modal-fixed-footer">
				<div class="modal-content">
	<form action="my-account.php" method="post" enctype="multipart/form-data">
		<h6>Upload a profile image</h6>
		<br><br><div style="text-align: center;">
			<img style="height: 150px" src="images/profile.png">
		<br><br><br><br><input type="file" name="profileimg">
						</div>
		</div>
				<div class="modal-footer">
				<input class="btn" type="submit" name="uploadprofileimg" value="Upload Image">
	</form>
				</div>
			</div>     
		</div>
		</div>

	</div>
