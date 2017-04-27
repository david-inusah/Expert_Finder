<?php
class Comment {
        public static function createComment($commentBody, $postId, $userId) {
                if (strlen($commentBody) > 160 || strlen($commentBody) < 1) {
                        die('Incorrect length!');
                }
                if (!DB::query('SELECT id FROM posts WHERE id=:postid', array(':postid'=>$postId))) {
                        echo 'Invalid post ID';
                } else {
                        DB::query('INSERT INTO comments VALUES (\'\', :comment, :userid, NOW(), :postid)', array(':comment'=>$commentBody, ':userid'=>$userId, ':postid'=>$postId));
                                Notify::createCommentsNotify($commentBody);
                }
        }
        public static function displayComments($postId) {
                $cmts="";
                $comments = DB::query('SELECT comments.comment, users.firstname, users.lastname, users.profileimg FROM comments, users WHERE post_id = :postid AND comments.user_id = users.id', array(':postid'=>$postId));
                foreach($comments as $comment) {
                        $firstname=$comment['firstname'];
                        $lastname=$comment['lastname'];
                        $profileimg=$comment['profileimg'];
                        if (strlen($profileimg)==0) {
                                $profileimg='images/profile.png';
                        }
                        $cmts.= "<li style='height: 40px' class='collection-item avatar'>
                                <img src=".$profileimg." class='circle' style='width=10px;'> ".$comment['firstname']." ".$comment['lastname']."
                                <p>".$comment['comment']."</p>
                        </li>";
                }

                return $cmts;
        }
}
?>