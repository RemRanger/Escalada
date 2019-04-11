<?php
header("Content-Type:application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
session_start();
include_once 'dbconnect.php';

// if (isset($_SESSION['IdUser']) != "")
// {
	// header("Location: index.php");
	// //echo 'Logged in already';
// }

$userName = mysqli_real_escape_string($conn, $_POST['userName']);
$upass = mysqli_real_escape_string($conn, $_POST['password']);

$res=$conn->query("SELECT Id, Password, FirstName FROM User WHERE UserName='$userName'");
$row=$res->fetch_assoc();

echo '{';
echo '"id": ';
if($row['Password'] == md5($upass))
	echo $row['Id'];
else
	echo '-1';

echo ', ';
echo '"firstName": "' . $row['FirstName'] . '"';

echo '}';
?>
