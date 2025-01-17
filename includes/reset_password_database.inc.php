<?php
$Database_Server = "localhost";
$Database_Username = "root";
$Database_Password = "";
$Database_Name = "Linux_Lab";

//create the connection
$connection = mysqli_connect($Database_Server, $Database_Username, $Database_Password, $Database_Name);

//check the connection
if (!$connection) {
    die("Connection Failed: " . $mysqli_connection_error());
}