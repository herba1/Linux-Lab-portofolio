<?php 
require_once "includes/config_session.inc.php";
require_once "includes/signup_view.inc.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
  <h3>Log In </h3>
  <div id = "login-form"> 
    <form action="includes/login.inc.php" method="post">
        <input type="text" name="username" placeholder="Username">
        <input type="password" name="password" placeholder="Password">
        <button type="submit">Login</button>
    </form>
  </div>
  
<h3>Sign Up</h3>
  <div id = "signup-form"> 
    <form action="includes/signup.inc.php" method="post">
        <input type="text" name="name" placeholder="Name">
        <input type="text" name="username" placeholder="Username">
        <input type="email" name="email" placeholder="E-Mail">
        <input type="password" name="password" placeholder="Password">
        <input type="password" name="repeat password" placeholder="Repeat Password">
        <button>Sign Up</button>
    </form>
	<?php 
		check_signup_errors();
	?>
  </div>
    </body>
</html>
