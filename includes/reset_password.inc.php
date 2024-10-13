<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {  
    $email = $_POST["email"];

    try {
            require_once "database.inc.php";
            require_once "reset_password.contr.php";
            require_once "reset_password_model.inc.php";
            $errors = [];
            if (is_input_empty($email)) {
                $errors["empty_input"] = "Fill In The Field";
            }

            if (is_email_valid($email) === false) {
                $errors["email_invalid"] = "Invalid Email Format";
            }
            
            if (email_is_registered($pdo, $email) === false) {
                $errors["invalid_email"] = "Email Is Not Registered";
            }
            require_once "config_session.inc.php";
            if ($errors) {
                $_SESSION["errors_reset_password"] = $errors; 
                        header("Location: reset_info.inc.php");	
                //if (isset($_SESSION["errors_reset_password"])) {
                  //      echo "valid?";
                //}
                    //      foreach($errors as $error) {
                 //           echo "<br>";
                   //        echo $error;
                      //  }
                      exit();
            }

    } catch (PDOException $e) {
         echo "Caught exception: " . $e->getMessage();
    }
}
else die("Request Method Invalid");
 