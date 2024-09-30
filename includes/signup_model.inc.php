<?php

//type decleration
declare(strict_types=1);

function GetUsername(object $pdo, string $username) : array|false {
	$query = "SELECT username FROM users WHERE username = :username;";
	$statement = $pdo->prepare($query);
	$statement->bindParam(":username", $username);
	$statement->execute();

	$result = $statement->fetch(PDO::FETCH_ASSOC);
	return $result;
}

function GetEmail(object $pdo, string $email) : array|false {
	$query = "SELECT username FROM users WHERE email = :email;";
	$statement = $pdo->prepare($query);
	$statement->bindParam(":email", $email);
	$statement->execute();

	$result = $statement->fetch(PDO::FETCH_ASSOC);
	return $result;
}



