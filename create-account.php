<?php
include('classes/DB.php');
include('classes/Login.php');

$errormsg="";
if (isset($_POST['createaccount'])) {
    $username = $_POST['username'];
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];
    $email = $_POST['email'];
    $success=False;
        // $errormsg="";
    if (strlen($username)!=0 || strlen($password1)!=0 || strlen($password2)!=0 || strlen($email)!=0) {
        $errormsg = Login::create_account($username,$password1,$password2,$email);
} else{
    $errormsg= "Please fill all fields";
} 

} 

?>
<html>

<head>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
        background-image: url("images/background2-Copy.jpg");
        background-repeat: no-repeat;
    background-position: 50% 50%;
    background-clip: padding-box;
      /*background: #fff;*/
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

      <h5 class="red-text text darken-2">Welcome, New user? Join us!</h5>
      <h6 class="green-text"><?php echo $errormsg;?></h6>
      <!-- <div class="section"></div> -->

      <div class="container">
        <div class="z-depth-1 grey lighten-4 row" style="display: inline-block; padding: 32px 48px 0px 48px; border: 1px solid #EEE;">

          <form class="col s12" method="post" style="width: 300px" action="create-account.php">
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
                <input class='validate' type='email' name='email' id='email' />
                <label for='email'>Enter your email</label>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='password' name='password1' id='password1' />
                <label for='password1'>Enter password</label>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='password' name='password2' id='password2' />
                <label for='password2'>Retype password</label>
              </div>
            </div>
            <br />
            <center>
              <div class='row'>
                <button type='submit' name='createaccount' class='col s12 btn btn-large waves-effect red darken-2'>Sign Up</button>
              </div>
            </center>
          </form>
        </div>
      </div>
      <a href="login.php" class="red-text text darken-2">Login</a>
    </center>

    <div class="section"></div>
    <div class="section"></div>
  </main>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.1/jquery.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>
</body>
</html>