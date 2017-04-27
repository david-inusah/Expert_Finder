<?php

class Post{
    public static function createPost($postbody, $loggedInUserId, $profileUserId) {
        if (strlen($postbody) > 160 || strlen($postbody) < 1) {
            die('Incorrect length!');
        }
        if ($loggedInUserId == $profileUserId) {
          if (count(Notify::createMentionsNotify($postbody)) != 0) {
            foreach (Notify::createMentionsNotify($postbody) as $key => $n) {
                $s = $loggedInUserId;
                $r = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$key))[0]['id'];
                if ($r != 0) {
                    DB::query('INSERT INTO notifications VALUES (\'\', :type, :receiver, :sender, :extra,0)', array(':type'=>$n["type"], ':receiver'=>$r, ':sender'=>$s, ':extra'=>$n["extra"]));
                }
            }
        }
        DB::query('INSERT INTO posts VALUES (\'\', :postbody, NOW(), :userid, 0, \'\', \'\')', array(':postbody'=>$postbody, ':userid'=>$profileUserId));
    } else {
        die('Incorrect user!');
    }
}
public static function createImgPost($postbody, $loggedInUserId, $profileUserId) {
    if (strlen($postbody) > 160) {
        die('Incorrect length!');
    }
    if ($loggedInUserId == $profileUserId) {
      if (count(Notify::createMentionsNotify($postbody)) != 0) {
        foreach (Notify::createMentionsNotify($postbody) as $key => $n) {
            $s = $loggedInUserId;
            $r = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$key))[0]['id'];
            if ($r != 0) {
                DB::query('INSERT INTO notifications VALUES (\'\', :type, :receiver, :sender, :extra)', array(':type'=>$n["type"], ':receiver'=>$r, ':sender'=>$s, ':extra'=>$n["extra"]));
            }
        }
    }
    DB::query('INSERT INTO posts VALUES (\'\', :postbody, NOW(), :userid, 0, \'\', \'\')', array(':postbody'=>$postbody, ':userid'=>$profileUserId));
    $postid = DB::query('SELECT id FROM posts WHERE user_id=:userid ORDER BY ID DESC LIMIT 1;', array(':userid'=>$loggedInUserId))[0]['id'];
    return $postid;
} else {
    die('Incorrect user!');
}
}

public static function deletePost($postid, $followerid){
    $postDeleted=False;
    if (DB::query('SELECT id FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid))) {
        DB::query('DELETE FROM posts WHERE id=:postid and user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));
        DB::query('DELETE FROM post_likes WHERE post_id=:postid', array(':postid'=>$_GET['postid']));
        $postDeleted=True;
        // Notify::deleteMentionsNotify($postid);
    }
    return $postDeleted;
}

public static function likePost($postId, $likerId) {
  $result="";
    if (!DB::query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId))) {
        DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid'=>$postId));
        DB::query('INSERT INTO post_likes VALUES (\'\', :postid, :userid)', array(':postid'=>$postId, ':userid'=>$likerId));
        Notify::createLikesNotify($postId);
        $result='like';
    } else {
        DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$postId));
        DB::query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId));
        $result='unlike';
    }
    return $result;
}
public static function link_add($text) {
    $text = explode(" ", $text);
    $newstring = "";
    foreach ($text as $word) {
        if (substr($word, 0, 1) == "@") {
            $newstring .= "<a href='profile.php?username=".substr($word, 1)."'>".htmlspecialchars($word)."</a> ";
        } else {
            $newstring .= htmlspecialchars($word)." ";
        }
    }
    return $newstring;
}
private static function getProfilePagePosts($userid){
   $userposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY posts.id DESC', array(':userid'=>$userid));
   return $userposts;    
}

public static function displayProfilePagePosts($userid, $username, $loggedInUserId) {
    $dbposts = self::getProfilePagePosts($userid);
    $posts = "";
    foreach($dbposts as $p) {
            $username = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['username'];
            $comments = DB::query('SELECT count(id) FROM comments WHERE post_id=:pid',array(':pid'=>$p['id']))[0]['count(id)'];
           $posts .= '<a href="profilepost.php?username='.$username.'&postid='.$p['id'].'">
           <div class="col s2" style="height:400px;">
              <div class="card hoverable grey lighten-5 z-depth-1">
                  <div style="height:200px;" class="card-image responsive-img">';
              // echo $p['postimg'];
                    if ($p['postimg']!="") {
                        // echo $p['postimg'];
                        $posts.='<img style="width:180px;height:200px; margin:0 auto;padding-top:10px;" src="'.$p['postimg'].'">';
                    }else{
                        $posts.='<img style="width:180px;height:200px; margin:0 auto; padding-top:10px;" src="images/nopreview.png">';
                    }
                    $posts.='</div>';
                    if ($p['body']!="") {
                       $posts.='<div class="card-content">
                      <p>'.self::link_add($p['body']).'</p>
                  </div>';
                    }
                    
                  $posts.='<div class="card-action">
                      <img src="images/heart-empty.png" style="width:10px;"> '.$p['likes'].'
                      <i class="tiny grey-text material-icons" style="margin-left: 20px;">comment</i> '.$comments;
                      if ($userid == $loggedInUserId) {
                        $posts .='<a href="profile.php?deletepost&username='.$username.'&postid='.$p['id'].'" style="margin-left: 15px;"><i class="tiny grey-text material-icons">delete</i></a>';
                    }
                    $posts .='</div>
                </div>
            </div>
            </a>
            ';
    }
    return $posts;
}

private static function getNewsFeedPosts($userid){
    $followingposts = DB::query('SELECT posts.id, posts.body, posts.postimg, posts.likes, users.`username`, posts.user_id FROM users, posts, followers
        WHERE posts.user_id = followers.user_id
        AND users.id = posts.user_id
        AND follower_id = :userid
        ORDER BY posts.id DESC;', array(':userid'=>$userid));
    return $followingposts;    
}
public static function getPost($postid){
  $post=array();
   if (DB::query('SELECT id FROM posts WHERE id=:postid',array(':postid'=>$postid))) {
      $post = DB::query('SELECT id, body, postimg, likes, user_id FROM posts
        WHERE posts.id = :postid;', array(':postid'=>$postid));
   }
    return $post;    
}

public static function  displayNewsFeedPosts($username, $loggedInUserId) {
    $dbposts = self::getNewsFeedPosts($loggedInUserId);
    
    $posts = "";
     $posts .= ' <div class="row">';
    foreach($dbposts as $p) {
        $userid=$p['user_id'];
        $postowner=$p['username'];
        $comments = DB::query('SELECT count(id) FROM comments WHERE post_id=:pid',array(':pid'=>$p['id']))[0]['count(id)'];
        $firstname= DB::query('SELECT firstname FROM users WHERE username=:username', array(':username'=>$postowner))[0]['firstname'];
        $lastname=DB::query('SELECT lastname FROM users WHERE username=:username', array(':username'=>$postowner))[0]['lastname'];
           $posts .= '<a href="post.php?username='.$postowner.'&postid='.$p['id'].'">
            <div class="col s2" style="height:400px;">
              <div class="card hoverable grey lighten-5 z-depth-1">
                  <div style="height:200px;" class="card-image responsive-img">';
              // echo $p['postimg'];
                    if ($p['postimg']!="") {
                        // echo $p['postimg'];
                        $posts.='<img style="width:180px;height:200px; margin:0 auto;padding-top:10px;" src="'.$p['postimg'].'">';
                    }else{
                        $posts.='<img style="width:180px;height:200px; margin:0 auto; padding-top:10px;" src="images/nopreview.png">';
                    }
                    $posts.='</div>
                     <h6 style="text-align:center;" class="grey-text text darken-4">by <b><a class="orange-text text darken-4" href="profile.php?username='.$postowner.'">'.$firstname.' '.$lastname.'</a></b></h6>';
                    if ($p['body']!="") {
                       $posts.='<div class="card-content">
                      <p>'.self::link_add($p['body']).'</p>
                  </div>';
                    }
                    
                  $posts.='<div class="card-action">
                      <img src="images/heart.png" style="width:10px;"> '.$p['likes'].'
                      <i class="tiny grey-text material-icons" style="margin-left: 95px;">comment</i> '.$comments.'
                      </div>
                </div>
            </div>
           </a>';
    }
    return $posts;
}
}
?>