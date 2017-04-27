<?php
include('classes/DB.php');
include ('classes/Login.php');
$errormsg="";
if(isset($_POST['login'])){
	$username=$_POST['username'];
	$password=$_POST['password'];

 $errormsg=Login::loginUser($username,$password); 
}	
?>

<html>

<head>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css">
  <script src="lib/SendBird.min.js"></script>
  <script type="text/javascript">
    var sb = new SendBird({
    appId: APP_ID;
});
    sb.connect(userId, function(user, error) {});
  </script>
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
      background: #fff;
    }

    .input-field input[type=date]:focus + label,
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
      box-shadow: none;
    }
  </style>
</head>

<body>
  <div class="section"></div>
  <main>
    <center>
      <img class="responsive-img" style="width: 250px;" src="images/fries.jpg" />
      <!-- <div class="section"></div> -->

      <!-- <h5 class="indigo-text">Patatte</h5> -->
      <h6 class="green-text"><?php echo $errormsg;?></h6>

      <div class="section"></div>

      <div class="container">
        <div style="width: 300px;" class="z-depth-1 grey lighten-4 row" style="display: inline-block; padding: 32px 48px 0px 48px; border: 1px solid #EEE;">
        	<div style="width: 200px;">
           <div class="section"></div>
           <h5 class="red-text text darken-2">Login</h5>
           <form class="col s12" action="login.php" method="post">
            <div class='row'>
              <div class='col s12'>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='username' id='username' />
                <label for='username'>Username</label>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='password' name='password' id='password' />
                <label for='password'>Password</label>
              </div>
             <!--  <label style='float: right;'>
								<a class='pink-text' href='#!'><b>Forgot Password?</b></a>
							</label> -->
            </div>

            <br />
            <center>
              <div class='row'>
                <button type='submit' name='login' class='col s12 btn btn-large waves-effect red darken-2'>Login</button>
              </div>
            </center>
          </form>
        </div>
      </div>
    </div>
    <a href="create-account.php" class="red-text text darken-2">Create account</a>
  </center>

  <div class="section"></div>
  <div class="section"></div>
</main>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>
</body>

</html>