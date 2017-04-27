<?php
include('./classes/Login.php');
include('./classes/DB.php');

if (isset($_GET['username'])) {
	# code...
	$username=$_GET['username'];
// echo $username;
	if(Login::isLoggedIn()){
		// echo "string";
		$userid=Login::isLoggedIn();
		// echo $username;
		if (Login::firstLogin($userid)) {
			if (DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))) {
			$username = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['username'];			
			// echo $username;
		}else{
			die("<img align='middle' style='padding: 100px 400px;' src='images/404.jpg'>");
		}
		}else{
			die("<img align='middle' style='padding: 100px 400px;' src='images/404.jpg'>");
		}
	}else{
		die("<img align='middle' style='padding: 100px 400px;' src='images/404.jpg'>");
	}

	if (isset($_POST['firstname'])) {
		$firstname = $_POST['firstname'];
		DB::query('UPDATE users SET firstname=:firstname WHERE id=:userid', array(':firstname'=>$firstname,':userid'=>$userid));
	}

	if (isset($_POST['lastname]'])) {
		$lastname = $_POST['lastname'];
		DB::query('UPDATE users SET lastname=:lastname WHERE id=:userid', array(':lastname'=>$lastname,':userid'=>$userid));
	}

	if (isset($_POST['bio'])) {
		$bio = $_POST['bio'];
		DB::query('UPDATE users SET bio=:bio WHERE id=:userid', array(':bio'=>$bio,':userid'=>$userid));
		// echo "bio added";
	}
	if (isset($_POST['skills'])) {
		$skills_array = explode(',', $_POST['skills']);
		for ($i=0; $i< count($skills_array); $i++) { 
			$skill = $skills_array[$i];
			if(!DB::query('SELECT skill FROM skills WHERE skill=:skill', array(':skill'=>$skill))) {
				DB::query('INSERT INTO skills VALUES (\'\', :skill)', array(':skill'=>$skill));
			}
			$skillid=DB::query('SELECT id FROM skills WHERE skill=:skill', array(':skill'=>$skill))[0]['id'];
			DB::query('INSERT INTO user_skills VALUES (\'\', :skillid, :userid)', array(':skillid'=>$skillid, ':userid'=>$userid));
// echo "skills added";
		}
	}
	if (isset($_POST['worklocation'])) {
		$worklocation=$_POST['worklocation'];
		DB::query('UPDATE users SET worklocation=:worklocation WHERE id=:userid', array(':worklocation'=>$worklocation,':userid'=>$userid));
	// echo "worklocation added";
		header('Location: recommended_users.php?username='.$username);
	}
}else{
	// die('Unauthorized view');
	die("<img align='middle' style='padding: 100px 400px;' src='images/404.jpg'>");
}


?>
<!-- <h3>Welcome <'?php echo $username;?></h3>
<form action="profile_info.php?username=<'?php echo $username; ?>" method="post">
	<p>Bio: <input type="text" name="bio" value=""></p>
	<p>Skills: <input type="text" name="skills" value=""></p>
	<p>Workshed Location: <input type="text" name="worklocation" value=""></br>
		<span>Separate multiple skills with ','</span></p>

		<input type="submit" name="done" value="Done">
	</form> -->
	<html>

<head>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css">
  <style>
    body {
      display: flex;
      min-height: 100vh;
      flex-direction: column;
    }

    main {
      flex: 1 0 auto;
    }

    body {
        background-image: url("images/chips.png");
        background-repeat: no-repeat;
    background-position: 50% 50%;
    background-clip: padding-box;
      /*background: #fff;*/
    }

   /* .input-field input[type=date]:focus + label,
    .input-field input[type=text]:focus + label,
    .input-field input[type=email]:focus + label,
    .input-field input[type=password]:focus + label {
      color: #e91e63;
    }

    .input-field input[type=date]:focus,
    .input-field input[type=text]:focus,
    .input-field input[type=email]:focus,
    .input-field input[type=password]:focus {
      border-bottom: 2px solid #e91e63;
      box-shadow: none;*/
    }
  </style>
</head>

<body>
  <div class="section"></div>
  <main>
    <center>
      <!-- <img class="responsive-img" style="width: 250px;" src="images/fries.jpg" /> -->
      <!-- <div class="section"></div> -->

      <h5 class="black-text">Profile Details</h5>
	<h6 class="green-text">Almost there. Just two more steps</h6>	      
      <!-- <h6 class="green-text"><?php echo $errormsg;?></h6> -->
      <!-- <div class="section"></div> -->

      <div class="container">
        <div class="z-depth-1 transparent row" style="display: inline-block; padding: 32px 48px 0px 48px; border: 1px solid #EEE;">

          <form class="col s12" method="post" style="width: 800px" action="profile_info.php?username=<?php echo $username; ?>">
            <!-- <div class='row'>
              <div class='col s12'>
              </div>
            </div> -->
            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='firstname' id='firstname' />
                <label for='firstname'>Firstname</label>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='lastname' id='lastname' />
                <label for='lastname'>Lastname</label>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='bio' id='bio' />
                <label for='bio'>Bio</label>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='skills' id='skills' />
                <label for='skills'>Skills</label>
                <span><h6 class="green-text" style="float: left;">Separate multiple skills with a ','</h6></span>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='worklocation' id='worklocation'/>
                <label for='worklocation'>Workshed Location</label>
              </div>
            </div>

            <br />
            <center>
              <div class='row' style="width: 100px; float: right;">
                <button type='submit' name='recommended_users.php' class='col s12 btn btn-small waves-effect red darken-2'>Done</button>
              </div>
            </center>
          </form>
          <h6 class="cyan-text darken-3" style="text-align: center;">Account Details Progress: 50%</h6><br>

          <div class="progress">
      <div class="determinate" style="width: 50%"></div>
  </div>
        </div>
      </div>
    </center>
    <div class="section"></div>
    <div class="section"></div>
  </main>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.1/jquery.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>
</body>
</html>