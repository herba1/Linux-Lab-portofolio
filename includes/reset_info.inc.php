<?php
    require_once "config_session.inc.php";
    require_once "reset_password_view.inc.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h3>Reset Password</h3>
    <form action="reset_password.inc.php" method="POST" >
        <input type="text" name="email" placeholder="E-Mail">
        <button>Send Email</button>
    </form>
    <?php
        check_reset_password_errors();
        ?>
</body>
</html>
