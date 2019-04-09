<?php
header("Content-Type:application/json");
header("Access-Control-Allow-Origin: *");
session_start();
include_once 'dbconnect.php';

$IdLocation = $_GET["IdLocation"];

$sql = "
select Rou.id, Rou.color, Rou.name, Rou.type, Rou.rating, Rou.sublocation, Rou.dateFrom, Rou.dateUntil, Rou.pictureFileName, 
	(select Max(Result) from Attempt where IdRoute = Rou.Id) result,
	(select Max(Percentage) from Attempt where IdRoute = Rou.Id) percentage
from Route Rou 
where Rou.IdLocation = $IdLocation
";

$result = $conn->query($sql);

if ($result->num_rows > 0) 
{
	$outp = $result->fetch_all(MYSQLI_ASSOC);

	echo json_encode($outp, JSON_NUMERIC_CHECK);
} 

$conn->close();
?>
