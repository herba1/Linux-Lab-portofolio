<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$username = $_POST["username"]	
	$pwd = $_POST["pwd"]	
	$email = $_POST["email"]	

	try {
		require_once "database.inc.php";
	  	require_once "signup_model.inc.php";
		require_once "signup_contr.inc.php";	
		// Error Handlers
	$errors = [];
	
	if (is_input_empty($username, $pwd, $email)) {
		$errors["empty_input"] = "Fill in all fields!";		
	}
	
	if (!is_email_valid($email)) {
		$errors["invalid_email"] = "Invalid E-Mail!";		
	}
	
	if (username_is_taken($pdo, $username)) {
		$errors["invalid_username"] = $username . " is taken. Please Choose Another!";		
	}
	
	if (email_is_registered($pdo, $email)) {
		$errors["invalid_email"] = $username . $email . " is taken. Please Choose Another!";		
	}

	require_once "config_session.inc.php";	
	if ($errors) {
		$_SESSION["error_signup"] = $errors; 
		header("Location: ../index.php");
	}

	catch(PDOException $e) {
		die("Query Failed: " . $e->getMessage());
	}
} else {
	header("Location: ../index.php");
	die();
}
