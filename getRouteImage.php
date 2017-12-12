<?php
session_start();
include_once 'dbconnect.php';

$Id = $_GET["Id"];

$sql = "SELECT Picture FROM Route WHERE Id = $Id";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) 
{
	header("Content-type: image/png");
	echo $row["Picture"];
}
else
	die(mysqli_error($conn))
?>