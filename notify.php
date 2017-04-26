<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Notify.php');
include('./classes/Post.php');
include('./classes/RequestCollaboration.php');

$username="";
$response="";
$requestid="";
if (Login::isLoggedIn()) {
    $userid = Login::isLoggedIn();
    if (DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))) {
        $username = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['username'];
    }

} else {
  die("<img align='middle' style='padding: 100px 400px;' src='images/404.jpg'>");
}
$notifications="";
$responded=False;
if (isset($_GET['accepted'])) {
    $responded=True;
    $receiverid = $_GET['receiver'];
    $requestid= $_GET['requestid'];
    $requestid= $_GET['requestid'];
        // echo $requestid;
    if (RequestCollaboration::acceptRequest($requestid)) {
                // echo "we did it!";
        Notify::createRequestAcceptedNotify($userid,$receiverid);
    }else{
                // echo "did not update db";
    }
}
if (isset($_GET['rejected'])) {
    $responded=True;
    $receiverid = $_GET['receiver'];
    $requestid= $_GET['requestid'];
        // echo $requestid;
    if (RequestCollaboration::rejectRequest($requestid)) {
                // echo "we did it!";
        Notify::createRequestRejectedNotify($userid,$receiverid);

    }else{
                // echo "did not update db";
    }
}
if (DB::query('SELECT * FROM notifications WHERE receiver=:userid', array(':userid'=>$userid))) {
    $notification = DB::query('SELECT * FROM notifications WHERE receiver=:userid ORDER BY id DESC', array(':userid'=>$userid));
    foreach($notification as $n) {
        if ($n['type'] == 1) {
            $senderid = DB::query('SELECT id FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['id'];
            if ($senderid!=$userid) {
                $senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['username'];
                if ($n['extra'] == "") {
                    $notifications.="<h5 class='card-panel teal lighten-5'>You got a notification!<h5>";
                } else {
                    $extra = json_decode($n['extra']);
                    $notifications.= "<h6 class='card-panel teal lighten-5'>".Post::link_add('@'.$senderName)." mentioned you in a post! - ".$extra->postbody."</h6>";
                }
            }
        } else if ($n['type'] == 2) {
            $senderid = DB::query('SELECT id FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['id'];
            if ($senderid!=$userid) {
                $senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['username'];
                $notifications.= "<h6 class='card-panel teal lighten-5'>".Post::link_add('@'.$senderName)." liked your post!</h6>";
            }
        }else if ($n['type'] == 3) {
            $senderid = DB::query('SELECT id FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['id'];
            if ($senderid!=$userid) {
                $senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['username'];
                $notifications.= "<h6 class='card-panel teal lighten-5'>".Post::link_add('@'.$senderName)." commented your post!</h6>";
            }
        }else if ($n['type'] == 4) {
            $senderid = DB::query('SELECT id FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['id'];
            if ($senderid!=$userid) {
                $senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['username'];
                $notifications.= "<h6 class='card-panel teal lighten-5'>".Post::link_add('@'.$senderName)." followed you</h6>";
            }
        }else if ($n['type'] == 5) {
            $senderid = DB::query(  'SELECT id FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['id'];
            if ($senderid!=$userid) {
                $requestid=$n['extra'];

                $senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['username'];
                $notifications.= "<div class='card-panel teal lighten-5'><h6>".Post::link_add('@'.$senderName)." sent you a collaboration request</h6>";
                                // echo $requestid;
                if (RequestCollaboration::responded($requestid)) {
                                        // echo "wow";
                                        // echo $n['sender'];
                    $notifications.="<a class='col s12 btn btn-small disabled waves-effect #b2dfdb' href='notify.php?accepted' style='margin:0px 10px 0px 40px;'><font>Responded</font></a></div>";
                }else{
                                        // echo $n['sender'];
                    $notifications.="<a class='col s12 btn btn-small waves-effect #b2dfdb' href='notify.php?accepted&receiver=".$n['sender']."&requestid=".$requestid."' style='margin:0px 10px 0px 40px;'><font>Accept</font></a><a class='col s12 btn btn-small waves-effect #b2dfdb' href='notify.php?rejected&requestid=".$requestid."'><font>Reject</font></a></div>";}
                }
            }else if ($n['type'] == 6) {
                $senderid = DB::query('SELECT id FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['id'];
                if ($senderid!=$userid) {
                    $senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid'=>$senderid))[0]['username'];
                    $notifications.= "<h6 class='card-panel teal lighten-5'>".Post::link_add('@'.$senderName)." accepted your collaboration request!</h6>";
                }
            }else if ($n['type'] == 7) {
                $senderid = DB::query('SELECT id FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['id'];
                if ($senderid!=$userid) {
                    $senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid'=>$n['sender']))[0]['username'];
                    $notifications.= "<h6 class='card-panel teal lighten-5'>".Post::link_add('@'.$senderName)." rejected your collaboration request</h6>";
                }
            }
        }
        DB::query('UPDATE notifications SET seen=1 WHERE receiver=:userid AND seen=0',array(':userid'=>$userid));
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
      <!-- Import Google Icon Font -->
      <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <!-- Import materialize.css -->
      <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>

      <script type="text/javascript" src="js/modals.js" ></script>


      <!-- Let browser know website is optimized for mobile -->
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

      <style type="text/css">
       body {
        background-color:#b2dfdb;
        /*background-repeat: no-repeat;
    background-position: 50% 50%;
    background-clip: padding-box;*/
      /*background: #fff;
    }
    */
}
</style>
</head>

<body>

    <nav>

      <div class="nav-wrapper black darken-1"">
          <a href="index.php" class="brand-logo" style="margin-left: 20px"><h5>Patatte</h5></a>
          <ul id="nav-mobile" class="right hide-on-med-and-down">
            <li class="active"><a href="profile.php?username=<?php echo $username; ?>"><i class="material-icons">person</i></a></li>
            <li><a href="index.php">News Feed</a></li>
            <li><a href="collaboration_search.php">Collaborate</a></li>
            <li><a href="http://www.patatte.com/">Blog</a></li>
            <li><a href="notify.php"><span class="new badge"><?php echo Notify::newnotificationsCount($userid);?></span></a></li>
            <li><a href="my-account.php"><i class="material-icons">settings</i></a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>
<h4>Notifications</h4>
<?php
echo $notifications;
?>
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
</body>
</html>