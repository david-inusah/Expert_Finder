<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Image.php');
include('./classes/Notify.php');
include('./classes/RequestCollaboration.php');
include('./classes/FollowUser.php');

$isFollowing = False;
$requestedCollaboration = False;
$posts="";
$skills="";
if (isset($_GET['username'])) {
    $userid = Login::isLoggedIn();
    if (DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))) {
        $loggedInUsername = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['username'];
    }
    if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))) {
        $username = DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
        $firstname= DB::query('SELECT firstname FROM users WHERE username=:username', array(':username'=>$username))[0]['firstname'];
        $lastname=DB::query('SELECT lastname FROM users WHERE username=:username', array(':username'=>$username))[0]['lastname'];
        $userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];
        $bio = DB::query('SELECT bio FROM users WHERE username=:username', array(':username'=>$username))[0]['bio'];
        $skills_array = DB::query('SELECT `skill` from skills,user_skills,users WHERE username=:username and users.id=user_skills.user_id and user_skills.skill_id=skills.id', array(':username'=>$username));
        $followers = DB::query('SELECT count(follower_id) from users, followers WHERE username=:username and users.id=followers.user_id
            ', array(':username'=>$_GET['username']))[0]['count(follower_id)'];
        $following = DB::query('SELECT count(follower_id) FROM followers, users WHERE username=:username and followers.follower_id=users.id
            ', array(':username'=>$_GET['username']))[0]['count(follower_id)'];
        $followerid = Login::isLoggedIn();
        foreach ($skills_array as $key) {
            $skills.=$key['skill'].',';
        }
    }else{
        die("<img align='middle' style='padding: 100px 400px;' src='images/404.jpg'>");
    }
    $profileInfo = '<div class="col s10 offset-s1 center-align" ><h5>'.$bio.'</h5><br><h6><b>Skills:</b> '.$skills.'</h6><span style=padding-right:10px>followers: <sup>'.$followers.'</sup></span><span style=padding-left:10px>following: <sup>'.$following.'</sup></span></div><br>';
    if (isset($_GET['follow'])) {
        if (FollowUser::follow($userid, $followerid)){
            echo "User followed";
            $isFollowing = True;
        }else{
            echo "Unable to follow user";
        }                
    }
    if (isset($_GET['unfollow'])) {
     if (FollowUser::unfollow($userid, $followerid)){
            // Notify::createFollowNotify();
        echo "User unfollowed";
        $isFollowing = False;
    }else{
        echo "Unable to unfollow user";
    }       
}
if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                        //echo 'Already following!';
    $isFollowing = True;
}
if (isset($_GET['request_collaboration'])) {
    if (RequestCollaboration::sendRequest($userid, $followerid)){
        echo "Request sent";
        $requestedCollaboration = True;
    }else{
        echo "Unable to send request";
    }       
}
if (isset($_GET['deletepost'])) {
    $postid=$_GET['postid'];
    if (Post::deletePost($postid, $followerid)){
        echo 'Post deleted!';
    }
}
if (isset($_POST['post'])) {
    if ($_FILES['postimg']['size'] == 0) {
        Post::createPost($_POST['postbody'], Login::isLoggedIn(), $userid);
    } else {
        $postid = Post::createImgPost($_POST['postbody'], Login::isLoggedIn(), $userid);
        Image::uploadImage('postimg', "UPDATE posts SET postimg=:postimg WHERE id=:postid", array(':postid'=>$postid));
    }
}
if (isset($_GET['postid']) && isset($_GET['like'])) {
    Post::likePost($_GET['postid'], $followerid);
}
$posts = Post::displayProfilePagePosts($userid, $username, $followerid);
} else {
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

  <!-- Let browser know website is optimized for mobile -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

      <style type="text/css">
      .nav-wrapper {
            height: 50px;
        /*line-height: 50px;*/
    }
  </style>
</head>

<body>

    <nav>
        <div class="nav-wrapper black darken-1"">
          <a href="#" class="brand-logo"><h5>Patatte</h5></a>
          <ul id="nav-mobile" class="right hide-on-med-and-down">
            <li class="active"><a href="profile.php?username=<?php echo $loggedInUsername; ?>"><i class="material-icons">person</i></a></li>
            <li><a href="index.php">News Feed</a></li>
            <li><a href="collaboration_search.php">Collaborate</a></li>
            <li><a href="#!" class="collection-item dropdown-button"><span class="new badge"><?php echo Notify::notificationsCount($followerid);?></span></a></li>
            <!-- <li><a class="dropdown-button" href=""><i class="material-icons">notifications</i></a></li> -->
            <li><a class="dropdown-button" href="#!"> <i class="material-icons">more_vert</i></a></li>
            <!-- <li></li> -->
        </ul>
    </div>
</nav>
<div style="padding-left: 10px;padding-right: 10px; padding-bottom: 15px">
    <?php echo $profileInfo;?>
<div style="float: left;">
<img src="images/profile.png"  style="height:100px;" class="col s10 offset-s1 circle responsive-img">
<h5><?php echo $firstname." ".$lastname; ?></h5>
</div>

<!-- <span><a href="index.php">View Newsfeed</a></span></br></br> -->
<!-- <form action="profile.php?username='<"?php echo $username; ?>" method="post"> -->
<div style="margin: 0 auto; text-align: center; width:500px; border: 1px solid transpaent" >
    <?php
    if ($userid != $followerid) {

        if ($isFollowing) {
            echo '<div><a class="waves-effect waves-light btn" href="profile.php?unfollow&username='.$username.'">Unfollow</a></div></br>';
        } else {
            echo '<div><a class="waves-effect waves-light btn" href="profile.php?follow&username='.$username.'">follow</a></div></br>';
        }
    }
    ?>
<!-- </form> -->
<!-- <form action="profile.php?username=<'?php echo $username; ?>" method="post">
 -->    
 <?php
    if ($userid != $followerid) {
        if ($requestedCollaboration) {
            // echo "<div style='width: 150px; height: 60px; color: navy; background-color: pink; border: 2px solid blue;'><p>Pending Collaboration request</p></div></br>";

            echo '<div><a class="waves-effect waves-light btn disabled" href="profile.php?request_collaboration&username='.$username.'">Pending Response</a></br></div>';
        }else{
          echo '<div><a class="waves-effect waves-light btn" href="profile.php?request_collaboration&username='.$username.'">Collaborate</a></div>';
      }
  }
  ?>
<!-- </form> -->
</div></br></br></br>

<?php
// echo 'Bio: '.$bio.'</br>';
// echo 'Skills: '.$skills;
// echo '</br> followers: '.$followers;
// echo '</br> following: '.$following.'</br>';

if ($userid == $followerid) {
    echo '<h5>Make a post</h5><form action="profile.php?username='.$username.'" method="post" enctype="multipart/form-data">
    <textarea name="postbody" rows="8" cols="80"></textarea>
    <br />Upload an image:
    <input type="file" name="postimg">
    <input type="submit" name="post" value="Post">
</form>';
}
?>

<div class="row">
 <!--  <div class="col s2">
      <div class="card">
        <div style="height:200px;" class="card-image">
          <img src="https://s-media-cache-ak0.pinimg.com/originals/aa/dd/c1/aaddc1ab529b6cb3b01301965a8958fb.jpg">
          <span class="card-title" style="width:100%; background: rgba(0, 0, 0, 0.5);">Sample1</span>
      </div>
      <div class="card-content">
          <p>Hello World!</p>
      </div>
      <div class="card-action">
          <a href="#">This is a link</a>
      </div>
  </div>
</div> -->

<div class="posts">
    <?php 
    if (strlen($posts)>0){
        echo $posts; }else{
            echo "No posts yet";
        }?>
    </div>

    </div>
</div>

    <!-- <!--Import jQuery before materialize.js -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="js/materialize.min.js"></script>
</body>
</html>