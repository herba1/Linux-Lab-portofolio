<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
	$username = $_POST["username"];	
    $email = $_POST["email"];	
    $pwd = $_POST["pwd"];

    try {
        require_once 'database.inc.php';
		require_once 'config_session.inc.php';
        require_once 'signup_model.inc.php';
        require_once 'signup_contr.inc.php';	
	 
		$errors = [];
    
		if (is_input_empty($username, $pwd, $email)) {
		$errors["empty_input"] = "Fill In All Fields!";		
		}

		if (!is_email_valid($email)) {
            $errors["invalid_email"] = "Invalid E-Mail!";		
        }
    
        if (username_is_taken($pdo, $username)) {
            $errors["username_taken"] = "Username is taken. Please choose another!";		
        }
    
        if (email_is_registered($pdo, $email)) {
            $errors["email_taken"] = "Email already registered. Please choose another!";		
        }

		if (is_passsword_valid($pwd) === false) {
			$errors["password_ invalid"] = "Invalid Password! Passwords Must Contain:<br>
			- At Least 10 Characters<br>
			- 1 Number<br>
			- 1 Uppercase Character<br>
			- 1 Lowercase Character<br>
			- 1 Special Character<br>";
		}

	if ($errors) {
		$_SESSION["errors_signup"] = $errors; 
		 header("Location: ../index.php");	
		 die();
		}

	 create_user($pdo, $pwd, $username, $email);
	 header("Location: ../index.php?signup=success");
	 $pdo = null;
	 $stmt = null;	 
	 die();

	} catch (PDOException $e) {
        echo "Caught exception: " . $e->getMessage();
    }
}