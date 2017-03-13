<?php
session_start();
include_once 'dbconnect.php';

$idSession = $_GET["idSession"];
$idRoute = $_GET["idRoute"];
$includeRemoved = $_GET["includeRemoved"];

$sql = "
	select Rou.Id, Rou.Color, Rou.Type, Rou.Name, Rou.Rating, Rou.Sublocation 
	from Route Rou 
	join Session Ses on Ses.IdLocation = Rou.IdLocation
	where Ses.Id = $idSession
";
if ($includeRemoved == 0)
	$sql .= " and Rou.Removed = 0";

echo '<option disabled';
if (empty($idRoute))
	echo " selected";
echo '>--Please select a route--'.$idRoute.'</option>';

$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) 
{
	$bgcolor = $row["Color"];
	if (strtolower($bgcolor) == 'black')
		$textColor = 'white';
	else
		$textColor = 'black';
	echo '<option style="background-color: ' . $bgcolor . ';color:' . $textColor . '" value="' . $row["Id"]. '"';
	if ($row["Id"] == $idRoute)
		echo " selected";
	echo '>' . $row["Type"] . ': ' . $row["Name"] . ' (' . $row["Rating"] . ') - ' . $row["Sublocation"] . '</option>';
}
?>