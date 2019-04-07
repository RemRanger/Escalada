<?php
$servername = "localhost:3306";
$username = "remran1q";
$password = "Ioan316X4www";
$dbname = "remran1q_ClimbLog";
$IdUser = 1;

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) 
    die("Connection failed: " . $conn->connect_error);
?>