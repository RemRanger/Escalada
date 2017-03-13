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
$sql .= ' order by Rou.Type desc, Rou.Rating, Rou.Sublocation';

echo '<table Id="RouteTable" style="width: 100%">';

$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) 
{
	echo '  <tr Id="RouteRow_' . $row["Id"] . '" onClick="handleRouteRowClick(' . $row["Id"] . ');"';
  if ($row["Id"] == $idRoute)
		echo ' style="color: white; background-color: black; opacity: 0.5;"';
	echo '>';
	echo '    <td style="text-align: left">';
	echo '      <input Id="RouteRadio_' . $row["Id"] . '" type="radio" name="IdRoute" onClick="handleRouteClick(' . $row["Id"] . ');" value="' . $row["Id"] . '" ';
  if ($row["Id"] == $idRoute)
		echo ' checked';
	echo ' />';
	echo '    <td style="width: 16px; background-color: ' . $row["Color"] . '"; />';
	echo '<td style="text-align: left">' . $row[Name] . '</td>';
	echo '    <td nowrap>' . $row["Type"] . '</td>';
	echo '    <td nowrap>' . $row["Rating"] . '</td>';
	echo '    <td nowrap style="text-align: left">' . $row["Sublocation"] . '</td>';
	echo '  </tr>';
}

$currentRouteId = $idRoute;

echo '</table>';
