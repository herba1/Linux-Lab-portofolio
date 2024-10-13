<?php 
require_once "includes/config_session.inc.php";
require_once "includes/signup_view.inc.php";
require_once "includes/login_view.inc.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
  <h3>Log In </h3>
    <form action="includes/login.inc.php" method="post">
        <input type="text" name="username" placeholder="Username">
        <input type="password" name="pwd" placeholder="Password">
        <button>Login</button>
    </form>
    <?php 
        check_login_errors();
    ?>
  
<h3>Sign Up</h3>
    <form action="includes/signup.inc.php" method="post">
        <input type="text" name="username" placeholder="Username">
        <input type="text" name="email" placeholder="E-Mail">
        <input type="password" name="pwd" placeholder="Password">
        <button>Signup</button>
    </form>
	<?php 
		check_signup_errors();
	?>
<h3>Forgot Password?</h3>
    <form action="includes/reset_info.inc.php" method="post">
        <button>Reset Password</button>
        </form>
    </body>
</html>
