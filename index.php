<?php
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Post.php');
include('./classes/Notify.php');
include('./classes/Comment.php');
$showTimeline = False;
$username="";
$userid="";
if (Login::isLoggedIn()) {
  $userid = Login::isLoggedIn();
  if (DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))) {
    $username = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['username']; 
        // echo $username;
    $showTimeline = True;
  }else {
    die("<img align='middle' style='padding: 100px 400px;' src='images/404.jpg'>");
  }
 if (isset($_GET['postid']) && isset($_GET['like'])) {
  Post::likePost($_GET['postid'], $userid);
}
    // if (isset($_POST['comment'])) {
    //     Comment::createComment($_POST['commentbody'], $_GET['postid'], $userid);
    // }

  if (isset($_POST['searchbox'])) {
    $tosearch = explode(" ", $_POST['searchbox']);
    Search::userSearch($tosearch);
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

   <style type="text/css">
     #search {
    display: none;
    margin-bottom: 0px;
    border-top: 1px solid #111;
    border-bottom: 1px solid #111;
}
   </style>
   <script type="text/javascript">
     <script>
// Toggle search

    $('a#toggle-search').click(function()
    {
        var search = $('div#search');

        search.is(":visible") ? search.slideUp() : search.slideDown(function()
        {
            search.find('input').focus();
        });

        return false;
    });
</script>
   </script>
 </head>

 <body>

  <nav>
    <div class="nav-wrapper black darken-1"">
      <a href="index.php" class="brand-logo" style="margin-left: 20px"><h5>Patatte</h5></a>
      <ul id="nav-mobile" class="right hide-on-med-and-down">
        <li>
          <a id="toggle-search" href="#!">
            <i class="large mdi-action-search"></i>
          </a>
        </li>
        <li class="active"><a href="profile.php?username=<?php echo $username; ?>"><i class="material-icons">person</i></a></li>
        <li><a href="index.php">News Feed</a></li>
        <li><a href="collaboration_search.php">Collaborate</a></li>
        <li><a href="http://www.patatte.com/">Blog</a></li>
        <li><a href="notify.php"><span class="new badge"><?php echo Notify::newnotificationsCount($userid);?></span></a></li>
        <li><a href="my-account.php"><i class="material-icons">settings</i></a></li>
        <li><a href="logout.php">Logout</a></li>

        <!-- <li></li> -->
      </ul>
    </div>
  </nav>

  <div id="search" class="row white-text grey darken-3" >

    <div class="container">
        <form method="get" action="https://www.google.com/search">
            <input class="form-control" type="text" placeholder="Search ..." name="q"></input>

            <input type="hidden" value="makoframework.com" name="as_sitesearch"></input>
        </form>
    </div>
</div>

  <h3>News Feed</h3>
  <?php
  $posts = Post::displayNewsFeedPosts($username, $userid);
  if (strlen($posts)>0){
    echo $posts; 
  }else{
    echo '<div style="height: 100%; width: 100%; text-align: center;">
    <br><br><font style="font-size:50px; color: #bdbdbd;">No posts yet</font>
  </div>';
}}
?>



<!-- <!--Import jQuery before materialize.js -->
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
</body>
</html>