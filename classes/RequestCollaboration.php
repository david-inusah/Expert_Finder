<?php
/**
* 
*/
class RequestCollaboration
{
    // $requestid="";

    public static function sendRequest($userid, $followerid){
        $requestSent=False;
        if ($userid != $followerid) {
                DB::query('INSERT INTO collaboration_requests VALUES (\'\', :userid, :followerid,0 ,0)', array(':userid'=>$userid, ':followerid'=>$followerid));
                $requestid = DB::query('SELECT MAX(id) FROM collaboration_requests WHERE sender_id=:followerid AND receiver_id=:userid', array(':userid'=>$userid, ':followerid'=>$followerid))[0]['MAX(id)'];
                $requestSent = True;
                Notify::createRequestNotify($followerid, $userid, $requestid);
        }
        return $requestSent;
    }

    public static function allowAnotherRequest($userid, $followerid){
        $requestSent = False;
        //if request is pending (accepted=0 & rejected=0), user cannot send another request
        if (DB::query('SELECT id FROM collaboration_requests WHERE sender_id=:followerid AND receiver_id=:userid AND accepted =0 AND rejected=0', array(':userid'=>$userid, ':followerid'=>$followerid))) {
            $requestSent = True;
        }
        //if request is rejected, user can send another request
        if (DB::query('SELECT sender_id FROM collaboration_requests WHERE sender_id=:followerid AND receiver_id=:userid AND accepted =0 AND rejected=1', array(':userid'=>$userid, ':followerid'=>$followerid))) {
            $requestSent = False;
        }
        //if request is accepted user cannot send another request
        if (DB::query('SELECT sender_id FROM collaboration_requests WHERE sender_id=:followerid AND receiver_id=:userid  AND accepted =1 AND rejected=0', array(':userid'=>$userid, ':followerid'=>$followerid))) {
            $requestSent = True;
        }
        return $requestSent;
    }

     public static function responded($requestid){
        $responded=False;
        if (DB::query('SELECT id  FROM collaboration_requests WHERE id=:requestid AND (accepted=1 OR rejected=1)', array(':requestid'=>$requestid))) {
            $id=DB::query('SELECT id  FROM collaboration_requests WHERE id=:requestid AND (accepted=1 OR rejected=1)', array(':requestid'=>$requestid))[0]['id'];
                $responded=True;
        }
            return $responded;
        }

    public static function acceptRequest($requestid){
        $accepted=False;
        if (DB::query('SELECT id  FROM collaboration_requests WHERE id=:requestid', array(':requestid'=>$requestid))) {
            DB::query('UPDATE collaboration_requests SET accepted=1 WHERE id=:requestid', array(':requestid'=>$requestid));
            $accepted=True;
        }
            return $accepted;
        }

    public static function rejectRequest($requestid){
            $rejected=False;
        if (DB::query('SELECT id  FROM collaboration_requests WHERE id=:requestid', array(':requestid'=>$requestid))) {
            DB::query('UPDATE collaboration_requests SET rejected=1 WHERE id=:requestid', array(':requestid'=>$requestid));
            $rejected=True;
        }
            return $rejected;
        }
}
?>  