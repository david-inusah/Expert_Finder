<?php
/**
* 
*/
class Login
{	
	public static function loginUser($username,$password){
		$errormsg = "";
		if (strlen($username)!=0 || strlen($password)!=0) {
			if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))){
				if (password_verify($password, DB::query('SELECT password FROM users WHERE username=:username', array(':username'=>$username))[0]['password'])){
					// echo "Logged In!";
					$cstrong = True;
					$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
					$user_id = DB::query('SELECT id from users WHERE username=:username', array(':username'=>$username))[0]['id'];
					DB::query('INSERT INTO login_tokens VALUES (\'\',:token,:user_id)', array(':token'=>sha1($token), 'user_id'=>$user_id)); 
					setcookie("SID", $token, time()+60*60*24*7,'/', NULL, NULL, TRUE);
					setcookie("SSID", 1, time()+60*60*24*2,'/', NULL, NULL, TRUE); 
					header('Location: index.php');
				}else{
					$errormsg= "Incorrect Password";
			// header('Location: login.php/');
				}
			}else{
				$errormsg="User Not Registered";
			}
		}else{
			$errormsg= "Please fill all fields";
		}
		return $errormsg;
	}

	public static function isLoggedIn()
	{
		if(isset($_COOKIE['SID'])){
			if (DB::query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['SID'])))){
				$user_id=DB::query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['SID'])))[0]['user_id'];
				if(isset($_COOKIE['SSID'])){
					return $user_id;
				}else{
					$cstrong = True;
					$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
					DB::query('INSERT INTO login_tokens VALUES (\'\',:token,:user_id)', array(':token'=>sha1($token), 'user_id'=>$user_id));	
					DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['SID'])));

					setcookie("SID", $token, time()+60*60*24*7,'/', NULL, NULL, TRUE);
					setcookie("SSID", 1, time()+60*60*24*2,'/', NULL, NULL, TRUE);	

					return $user_id;
				}
			}
		}
		return false;
	}

	public static function firstLogin($userid){
		$firstLogin = False;
		$loginCount = DB::query('SELECT count(*) FROM login_tokens WHERE user_id=:userid', array(':userid'=>$userid))[0]['count(*)'];
		if ($loginCount==1){
			// echo $loginCount;
			$firstLogin=True;
			return $firstLogin;
		} 
	}

	public static function create_account($username,$password1,$password2,$email){
		$errormsg="";
		$success=False;
		if (!DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {
			if (strlen($username) >= 3 && strlen($username) <= 32) {
				if (preg_match('/[a-zA-Z0-9_]+/', $username)) {
					if (strlen($password1) >= 6 && strlen($password1) <= 60) {
						if($password1==$password2){
							if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
								if (!DB::query('SELECT email FROM users WHERE email=:email', array(':email'=>$email))) {
									DB::query('INSERT INTO users VALUES (\'\',  \'\', \'\', :username, :password, :email, \'0\', \'\', \'\', \'\')', array(':username'=>$username, ':password'=>password_hash($password1, PASSWORD_BCRYPT), ':email'=>$email));
									$success=True;
									echo "Success!";
								} else {
									$errormsg= 'Email in use!';
								}
							} else {
								$errormsg= 'Invalid email!';
							}
						}else{
							$errormsg= "Different passwords provided";
						}

					} else {
						$errormsg= 'Password should be 6 to 60 characters';
					}
				} else {
					$errormsg= 'Invalid username';
				}
			} else {
				$errormsg= 'Invalid username';
			}
		} else {
			$errormsg = 'Username already in use!';
		}
		if ($success) {
			self::signupLogin($username, $password1);
		}
		return $errormsg; 
	}
	
	public static function signupLogin($username, $password){
		$cstrong = True;
		$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
		$user_id = DB::query('SELECT id from users WHERE username=:username', array(':username'=>$username))[0]['id'];
		DB::query('INSERT INTO login_tokens VALUES (\'\',:token,:user_id)', array(':token'=>sha1($token), 'user_id'=>$user_id)); 
		setcookie("SID", $token, time()+60*60*24*7,'/', NULL, NULL, TRUE);
		setcookie("SSID", 1, time()+60*60*24*2,'/', NULL, NULL, TRUE); 
		header('Location: profile_info.php?username='.$username);
	}
}
?>