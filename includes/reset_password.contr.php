<?php

declare(strict_types=1);
function is_input_empty(string $email) : bool {
	if (empty($email)) {
		 return true;
	}
	else return false;
}

function is_email_valid(string $email) : bool {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    }
    else return false;
}
function email_is_registered(object $pdo, string $email) : bool {
	if (GetEmail($pdo, $email)) {
		return true;
	}
	else return false;
}