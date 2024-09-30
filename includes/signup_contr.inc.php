<?php 

//type declaration, if an int is declared an int, it cannot be converted 
declare(strict_types=1);

function is_input_empty(string $username, string $password, string $email) : bool {
	if (empty($username) || empty($password) || empty($email)) {
		 return true;
	}
	else return false;
}

//checks if the email format is valid 
function is_email_valid(string $email) : bool {
	if (filter_var($email, FILTER_VALIDATE_EMAIL))	{ 
		return true;
	}
	else return false;
}

function username_is_taken(object $pdo, string $username) : bool {
	if(GetUsername($pdo, $username)) {
		return true;
	}
	else return false;
}

function email_is_registered(object $pdo, string $email) : bool {
	if (GetEmail($pdo, $email) {
		return true;
	}
	else return false;
}
