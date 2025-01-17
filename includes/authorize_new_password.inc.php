<?php 
if (isset($_POST["reset-password-submit"])) {
$selector = $_POST["selector"];
$validator = $_POST["validator"];
$password = $_POST["pwd"];
$passwordRepeat = $_POST["selector"];

if (empty($password) || empty($passwordRepeat)) {
    header("Location: ../includes/index.php");
    exit();
}
else if ($password != $passwordRepeat) {
    header("Location: ../includes/index.php");
    exit(); 
}
$currentDate = date("U");
require "reset_password_database.inc.php";
$sql = "SELECT * FROM pwdReset WHERE pwdResetSelector=? AND pwdResetExpires >= ?";
$statement = mysqli_stmt_init($connection);
if (!mysqli_stmt_prepare($statement, $sql)) {
    die("Debug: SQL DELETE failed: " . mysqli_error($connection));
} else {
    echo "Debug: SQL DELETE prepared<br>";
    mysqli_stmt_bind_param($statement, "s", $selector);
    mysqli_stmt_execute($statement);
}
$result = mysqli_stmt_get_result($statement);
if (!$row = mysqli_fetch_assoc($result)) {
    echo "You need to re-submit your reset request";
    exit();
    } else {
        $tokenBinary = hex2bin($validator);
        $tokenCheck = password_verify($tokenBinary, $row["pwdResetToken"]);
        if ($tokenCheck === false) {
            echo "You need to re submit your reset request";
            exit();
        } elseif ($tokenCheck === true) {
            $tokenEmail = $row['pwdResetEmail'];
            $sql = "SELECT * FROM users WHERE emailusers=?;";
        }
    }
} 
else {
    header("Location: ../index.php");
}