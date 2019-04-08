<?php

header("Content-Type:application/json");
header("Access-Control-Allow-Origin: *");
session_start();
include_once 'dbconnect.php';

$sql = "SELECT FirstName, LastName, Gender, Id FROM User";
$result = $conn->query($sql);

echo "[";

if ($result->num_rows > 0) 
{
	$rowCount = 0;
    while ($row = $result->fetch_assoc()) 
    {
		if ($rowCount > 0)
			echo ",";
 		response($row["Id"], $row["Gender"], $row["FirstName"], $row["LastName"]);
 		
 		$rowCount++;
    }
}

echo "]";

$conn->close();

function response($id, $gender, $firstName, $lastName)
{
	$response['id'] = $id;
	$response['gender'] = $gender;
	$response['firstName'] = $firstName;
	$response['lastName'] = $lastName;

	$json_response = json_encode($response);
	echo $json_response;
}
?>

