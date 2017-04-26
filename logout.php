<?php
include('./classes/DB.php');
include('./classes/Login.php');
if (!Login::isLoggedIn()) {
  die("<img align='middle' style='padding: 100px 400px;' src='images/404.jpg'>");
}
        if (isset($_POST['alldevices'])) {
                DB::query('DELETE FROM login_tokens WHERE user_id=:userid', array(':userid'=>Login::isLoggedIn()));
        } else {
                if (isset($_COOKIE['SNID'])) {
                        DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['SNID'])));
                }
                setcookie('SNID', '1', time()-3600);
                setcookie('SNID_', '1', time()-3600);
        }
        header('Location: login.php');
?>