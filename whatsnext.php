<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<center>

<?php include_once 'mainmenu.php';?>

<?php
$IdUser = $_SESSION["IdUser"];
$Type = $_GET["Type"];
if (!isset($Type))
	$Type = "Toprope";

include_once 'dbconnect.php';
?>

<h1>What's next</h1>
Type:
<select id="RouteType" onChange="refresh()">
	<option <?php if ($Type == 'Toprope') echo ' selected' ?>>Toprope</option>
	<option <?php if ($Type == 'Lead') echo ' selected' ?>>Lead</option>
	<option <?php if ($Type == 'Boulder') echo ' selected' ?>>Boulder</option>
</select>
<br>
<br>

<?php
$sql = "
select * from
(
  select Rou.Id IdRoute, Rou.Color, Rou.Name, Rou.Rating, Loc.Name LocName, Rou.Sublocation, Rou.PictureFileName, Rou.DateUntil, Max(Att.Result) Result, Max(Att.Percentage) Percentage
  from Route Rou
  left outer join Attempt Att on Att.IdRoute = Rou.Id
	inner join Location Loc on Loc.Id = Rou.IdLocation
  where Rou.Type = '$Type' and Rou.DateUntil is null and (Att.IdUser = $IdUser or Att.IdUser is null)
  group by Rou.Id, Rou.Color, Rou.Name, Rou.Rating, Loc.Name
) Atm
where Result < 2 or Result is null
order by Rating, Result, Percentage desc
";
$result = $conn->query($sql);

if ($result->num_rows > 0) 
{
	echo '<table>';
	echo '  <tr><th colspan="2">Route</th><th><img src="result-finish.png"></th><th align="left">Venue</th><th align="left">Location</th><th/>';
	echo '</tr>';
	
	$lastRating = "";
	while ($row = $result->fetch_assoc()) 
	{
		if (is_null($row["Result"]))
			$resultPic = "result-not-climbed.png";
		else if ($row["Result"] == 0)
			$resultPic = "result-fail.png";
		else if ($row["Result"] == 1)
			$resultPic = "result-faults.png";
		else  if ($row["Result"] == 2)
			$resultPic = "result-success.png";
		
		if ($row["Rating"] != $lastRating)
		{
			echo '<tr style="background-color:rgba(0, 0, 0, 0.1)">';
			echo '<td colspan="100">';
			echo 'Rating: ' .$row["Rating"];
			echo '</td>';
			echo '</tr>';
		}

		echo '  <tr><td width="16" bgcolor="' . $row[Color] . ' "></td>';
		echo '<td><a href="userroutestats.php?IdRoute=' . $row["IdRoute"] . '&IdUser=' . $IdUser . '">';
		if ($row["DateUntil"] != null)
			echo '<del>';
		echo $row["Name"] . '</td></a>';
		if ($row["Result"] == 0 && $row["Percentage"] !== NULL)
			echo '<td style="color: red">' . $row[Percentage] . '%</td>';
		else
			echo '<td align="center"><img src="' . $resultPic . '"></td>';
		echo '<td>' . $row["LocName"] . '</td>';
		echo '<td>' . $row["Sublocation"] . '</td>';
		echo '</td>';
		echo '<td width="16px">';
		if ($row["PictureFileName"] != null)
			echo '<a href="RoutePictures/' . $row["PictureFileName"] . '" target="_blank"><img src="picture.png"></a>';
		echo '</td>';
		echo '</tr>';

		$lastRating = $row["Rating"];
	}
  echo "</table>";
} 
else 
{
    echo "No results";
}

$conn->close();

?>
<script>
function refresh()
{
	var routeTypeCombo = document.getElementById("RouteType");
	var routeType = routeTypeCombo.value;
	window.location = 'whatsnext.php?Type=' + routeType;
}
</script>
</body>
</html> 