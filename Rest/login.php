<?php
header("Content-Type:application/json");
header("Access-Control-Allow-Origin: *");
session_start();
include_once 'dbconnect.php';

// if (isset($_SESSION['IdUser']) != "")
// {
	// header("Location: index.php");
	// //echo 'Logged in already';
// }

$username = mysqli_real_escape_string($conn, $_POST['username']);
$upass = mysqli_real_escape_string($conn, $_POST['pass']);

$res=$conn->query("SELECT Id, Password FROM User WHERE UserName='$username'");
$row=$res->fetch_assoc();
if($row['Password'] == md5($upass))
	echo '{"id": ' . $row['Id'] . '}';
else
	echo '{"id": -1}';
?>
