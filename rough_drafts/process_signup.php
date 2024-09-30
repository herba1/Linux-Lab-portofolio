<?php
define("SQL_INSERT_USER", "INSERT INTO user (name, email, psw_hash) VALUES (?, ?, ?)");

if(empty($_POST['name'])) {
    die("Please Enter A Name");
}

if (empty($_POST['username'])) {
    die("Please Enter A Username");
}

if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    die("Please Enter A Valid Email");
}

$password = $_POST["password"];

// Password length validation
if (strlen($_POST["password"]) < 8 || strlen($_POST["password"]) > 25) {
    die("Password Must Be Between 8 - 25 Characters");
}

// Ensure the password contains at least one number
if (!preg_match("/[0-9]/", $_POST["password"])) {
    die("Password Must Contain At Least One Number");
}

// Ensure the password contains at least one letter (case-insensitive)
if (!preg_match("/[a-z]/i", $_POST["password"])) {
    die("Password Must Contain At Least One Letter");
}

// Ensure the password contains at least one special character
if (!preg_match('/[!@#$%^&*(),.?":{}|<>-_]/', $_POST["password"])) {
    die("Password Must Contain At Least One Special Character.");
}

// Check if password and repeated password match
if ($password !== $_POST["repeat_password"]) {
    die("Passwords Do Not Match!");
}

// Hash the password
$psw_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

// Connect to the database
$mysqli = require __DIR__ . "/database.php";

if ($mysqli->connect_errno) {
    die("Connection failed: " . $mysqli->connect_error);
}

// MySQL command to insert a user into the database
$sql = SQL_INSERT_USER;


$stmt = $mysqli->stmt_init();
/*
if (!$stmt->prepare($sql)) {
    echo "Failed to prepare statement. Error: " . $mysqli->error;
    die("MYSQL Connection Error");
}
*/
echo "Prepared statement is good\n";

// Bind the parameters
$stmt->bind_param("sss", $_POST["name"], $_POST["email"], $psw_hash);

// Execute the statement and check for errors
if ($stmt->execute()) {
    echo "Signup successful!";
} else {
    die("Signup Error: " . $stmt->error);
}

// Close the statement and the connection
$stmt->close();
$mysqli->close();
?>

