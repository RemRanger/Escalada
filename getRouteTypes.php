<?php
session_start();
include_once 'dbconnect.php';

$IdLocation = $_GET["IdLocation"];
$Type = $_GET["Type"];

if ($IdLocation != undefined)
{
	$sql = "select DefaultRouteType from Location Loc where Loc.Id = $IdLocation";
	$result = $conn->query($sql);
	if ($row = $result->fetch_assoc()) 
		$DefaultRouteType = $row["DefaultRouteType"];
}

$sql = "select RouteType from LocationRouteType where IdLocation = $IdLocation";
$result = $conn->query($sql);
echo '<select required name="Type"';
echo '>';
while ($row = $result->fetch_assoc()) 
{
	echo '  <option value="' . $row["RouteType"] . '"';
	if (isset($Type) || $result->num_rows < 2)
	{
		if ($row["RouteType"] == $Type || $result->num_rows < 2)
			echo ' selected';
	}
	else if ($row["RouteType"] == $DefaultRouteType)
		echo ' selected';
	echo '>' . $row["RouteType"]. '</option>';
}
echo '</select>';
?>